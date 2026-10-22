<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('auth:guest|127.0.0.1');
        RateLimiter::clear('auth:attacker@example.com|127.0.0.1');
        RateLimiter::clear('127.0.0.1');

        $this->category = Category::create([
            'name' => 'Workstations',
            'slug' => 'workstations',
            'description' => 'Developer Workstations',
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Security Hardened Server',
            'slug' => 'security-hardened-server',
            'tagline' => 'Enterprise Grade Server',
            'description' => 'Zero-trust architecture server.',
            'price' => 1500.00,
            'stock' => 10,
            'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 1. HTTP Security Headers & CSP (XSS & Clickjacking Defense)
    |--------------------------------------------------------------------------
    */

    public function test_security_headers_are_present_on_all_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertStringContainsString("default-src 'self'", $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString("frame-ancestors 'self'", $response->headers->get('Content-Security-Policy'));
        $response->assertHeader('Permissions-Policy');
    }

    /*
    |--------------------------------------------------------------------------
    | 2. XSS Input Sanitization
    |--------------------------------------------------------------------------
    */

    public function test_input_sanitizer_removes_script_tags_and_null_bytes(): void
    {
        $maliciousName = "Alice<script>alert('xss')</script>";
        $maliciousComment = "Great laptop!\0<script>evil()</script><iframe src='javascript:alert(1)'></iframe>";

        $response = $this->post("/product/{$this->product->id}/review", [
            'author_name' => $maliciousName,
            'rating' => 5,
            'headline' => 'Super secure',
            'comment' => $maliciousComment,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('reviews', [
            'author_name' => "Alice<script>alert('xss')</script>",
        ]);

        $this->assertDatabaseHas('reviews', [
            'product_id' => $this->product->id,
            'author_name' => 'Alice',
            'headline' => 'Super secure',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. CSRF Protection
    |--------------------------------------------------------------------------
    */

    public function test_csrf_middleware_rejects_missing_or_mismatched_token(): void
    {
        $middleware = new class(app(), app('encrypter')) extends \Illuminate\Foundation\Http\Middleware\PreventRequestForgery {
            protected function runningUnitTests()
            {
                return false;
            }
        };

        $request = \Illuminate\Http\Request::create('/cart/add', 'POST', [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);
        $session = app('session.store');
        $session->start();
        $session->put('_token', 'real-server-csrf-token-xyz');
        $request->setLaravelSession($session);

        $this->expectException(\Illuminate\Session\TokenMismatchException::class);
        $middleware->handle($request, fn () => response('ok'));
    }

    public function test_csrf_middleware_passes_when_valid_token_provided(): void
    {
        $middleware = new class(app(), app('encrypter')) extends \Illuminate\Foundation\Http\Middleware\PreventRequestForgery {
            protected function runningUnitTests()
            {
                return false;
            }
        };

        $token = 'matching-valid-csrf-token-12345';
        $request = \Illuminate\Http\Request::create('/cart/add', 'POST', [
            '_token' => $token,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);
        $session = app('session.store');
        $session->start();
        $session->put('_token', $token);
        $request->setLaravelSession($session);

        $response = $middleware->handle($request, fn () => response('token_verified', 200));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('token_verified', $response->getContent());
    }

    /*
    |--------------------------------------------------------------------------
    | 4. DoS / DDoS Protections (Rate Limiting & Throttling)
    |--------------------------------------------------------------------------
    */

    public function test_auth_rate_limiting_blocks_brute_force_after_5_attempts(): void
    {
        $credentials = [
            'email' => 'victim@example.com',
            'password' => 'wrong-password',
        ];

        // 5 allowed attempts
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', $credentials);
            $response->assertStatus(302);
            $response->assertSessionHasErrors('email');
        }

        // 6th attempt must be throttled with error or 429
        $throttled = $this->post('/login', $credentials);
        $throttled->assertSessionHasErrors('email');
        $errors = session('errors')->get('email');
        $this->assertStringContainsString('Too many authentication attempts', $errors[0]);
    }

    public function test_api_auth_rate_limiting_returns_429(): void
    {
        $payload = [
            'email' => 'attacker@example.com',
            'password' => 'invalid-password',
        ];

        for ($i = 0; $i < 5; $i++) {
            $res = $this->postJson('/api/auth/login', $payload);
            $res->assertStatus(401);
        }

        // 6th attempt triggers 429 Too Many Requests
        $resThrottled = $this->postJson('/api/auth/login', $payload);
        $resThrottled->assertStatus(429);
        $resThrottled->assertJsonPath('error', 'Too Many Requests');
    }

    public function test_order_lookup_is_rate_limited(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $res = $this->postJson('/api/orders/lookup', [
                'order_number' => 'SE-ORD-NONEXIST',
                'email' => 'test@example.com',
            ]);
            $res->assertStatus(404);
        }

        // 11th request triggers 429
        $throttled = $this->postJson('/api/orders/lookup', [
            'order_number' => 'SE-ORD-NONEXIST',
            'email' => 'test@example.com',
        ]);
        $throttled->assertStatus(429);
    }

    public function test_mega_payload_is_rejected_with_413_payload_too_large(): void
    {
        $hugeString = str_repeat('A', 3 * 1024 * 1024); // 3MB payload exceeds 2MB limit

        $response = $this->call(
            'POST',
            '/api/auth/login',
            [],
            [],
            [],
            [
                'CONTENT_LENGTH' => strlen($hugeString),
                'HTTP_ACCEPT' => 'application/json',
            ],
            $hugeString
        );

        $response->assertStatus(413);
    }

    public function test_api_pagination_bounds_prevent_dos_memory_exhaustion(): void
    {
        $response = $this->getJson('/api/products?per_page=9999999');

        $response->assertStatus(200);
        // per_page should be clamped to maximum 100
        $this->assertLessThanOrEqual(100, $response->json('pagination.per_page'));
    }

    public function test_cart_quantity_bounds_reject_excessive_quantities(): void
    {
        $response = $this->post('/cart/add', [
            'product_id' => $this->product->id,
            'quantity' => 1000000,
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    /*
    |--------------------------------------------------------------------------
    | 5. IDOR & Access Control Hardening
    |--------------------------------------------------------------------------
    */

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $userA = User::create([
            'name' => 'User A',
            'email' => 'usera@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        $userB = User::create([
            'name' => 'User B',
            'email' => 'userb@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        $orderA = Order::create([
            'order_number' => 'SE-ORD-SEC001',
            'user_id' => $userA->id,
            'customer_name' => 'User A',
            'customer_email' => 'usera@example.com',
            'customer_phone' => '1234567890',
            'shipping_address' => '123 Private St',
            'city' => 'SecCity',
            'postal_code' => '12345',
            'total_amount' => 1500.00,
            'status' => 'processing',
        ]);

        // User A (owner) can view
        $ownerRes = $this->actingAs($userA)->get("/order/{$orderA->order_number}");
        $ownerRes->assertStatus(200);

        // User B must be denied with 403 Forbidden
        $attackerRes = $this->actingAs($userB)->get("/order/{$orderA->order_number}");
        $attackerRes->assertStatus(403);
    }

    public function test_unauthenticated_stranger_cannot_view_order_without_session(): void
    {
        $order = Order::create([
            'order_number' => 'SE-ORD-GUEST99',
            'user_id' => null,
            'customer_name' => 'Guest Customer',
            'customer_email' => 'guest@example.com',
            'customer_phone' => '1234567890',
            'shipping_address' => '456 Guest St',
            'city' => 'Anytown',
            'postal_code' => '99999',
            'total_amount' => 300.00,
            'status' => 'pending',
        ]);

        // Stranger without session should be redirected to order lookup
        $res = $this->get("/order/{$order->order_number}");
        $res->assertRedirect('/order/lookup');
        $res->assertSessionHas('warning');
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Stock Integrity & Restoration on Cancel
    |--------------------------------------------------------------------------
    */

    public function test_cancelling_order_via_api_restores_product_stock(): void
    {
        $user = User::create([
            'name' => 'Buyer',
            'email' => 'buyer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        $initialStock = $this->product->stock; // 10

        $order = Order::create([
            'order_number' => 'SE-ORD-RESTORE01',
            'user_id' => $user->id,
            'customer_name' => 'Buyer',
            'customer_email' => 'buyer@example.com',
            'customer_phone' => '1234567890',
            'shipping_address' => '789 Tech Park',
            'city' => 'SecTown',
            'postal_code' => '54321',
            'total_amount' => 3000.00,
            'status' => 'Pending',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'unit_price' => 1500.00,
            'quantity' => 2,
            'subtotal' => 3000.00,
        ]);

        $this->product->decrement('stock', 2);
        $this->assertEquals($initialStock - 2, $this->product->fresh()->stock);

        $cancelRes = $this->actingAs($user, 'sanctum')->postJson("/api/orders/{$order->order_number}/cancel");
        $cancelRes->assertStatus(200);

        // Verify stock has been restored to initial
        $this->assertEquals($initialStock, $this->product->fresh()->stock);
    }
}
