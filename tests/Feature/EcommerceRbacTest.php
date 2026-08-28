<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcommerceRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'description' => 'Developer laptops'
        ]);
    }

    public function test_customer_can_register_and_access_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);

        $dashboardRes = $this->get('/dashboard');
        $dashboardRes->assertStatus(200);
        $dashboardRes->assertSee('Welcome back, John Doe!');
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Unauthorized access. Admin privileges required.');
    }

    public function test_admin_can_access_dashboard_and_manage_products(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $category = Category::first();

        $res = $this->actingAs($admin)->get('/admin/dashboard');
        $res->assertStatus(200);

        $storeRes = $this->actingAs($admin)->post('/admin/products', [
            'name' => 'Pro Workstation 16',
            'category_id' => $category->id,
            'tagline' => 'High performance',
            'description' => 'Best laptop for coders',
            'price' => 1999.99,
            'sale_price' => 1799.99,
            'stock' => 10,
            'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8',
        ]);

        $storeRes->assertSessionHas('success');
        $this->assertDatabaseHas('products', [
            'name' => 'Pro Workstation 16',
            'stock' => 10,
        ]);

        $product = Product::where('name', 'Pro Workstation 16')->first();

        $updateRes = $this->actingAs($admin)->put("/admin/products/{$product->id}", [
            'name' => 'Pro Workstation 16 V2',
            'category_id' => $category->id,
            'tagline' => 'High performance upgraded',
            'description' => 'Best laptop for coders',
            'price' => 1999.99,
            'stock' => 25,
            'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8',
        ]);

        $updateRes->assertSessionHas('success');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Pro Workstation 16 V2',
            'stock' => 25,
        ]);

        $deleteRes = $this->actingAs($admin)->delete("/admin/products/{$product->id}");
        $deleteRes->assertSessionHas('success');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_customer_order_placement_associates_user_id(): void
    {
        $customer = User::create([
            'name' => 'Alice Tech',
            'email' => 'alice@tech.io',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $product = Product::create([
            'category_id' => Category::first()->id,
            'name' => 'Wireless Mouse',
            'slug' => 'wireless-mouse',
            'description' => 'Precision mouse',
            'price' => 49.99,
            'stock' => 50,
            'image' => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7',
        ]);

        $cart = [
            $product->id => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => 49.99,
                'quantity' => 2,
                'image' => $product->image,
            ]
        ];

        $checkoutRes = $this->actingAs($customer)
            ->withSession(['cart' => $cart])
            ->post('/checkout/process', [
                'customer_name' => 'Alice Tech',
                'customer_email' => 'alice@tech.io',
                'customer_phone' => '1234567890',
                'shipping_address' => '123 Main St',
                'city' => 'Tech City',
                'postal_code' => '12345',
                'payment_method' => 'card',
            ]);

        $checkoutRes->assertSessionHas('success');

        $order = Order::where('customer_email', 'alice@tech.io')->first();
        $this->assertNotNull($order);
        $this->assertEquals($customer->id, $order->user_id);
        $this->assertEquals(48, $product->fresh()->stock);
    }

    public function test_guest_cannot_access_checkout_without_signing_in(): void
    {
        $response = $this->get('/checkout');
        $response->assertRedirect('/login');

        $processResponse = $this->post('/checkout/process', [
            'customer_name' => 'Guest User',
            'customer_email' => 'guest@example.com',
        ]);
        $processResponse->assertRedirect('/login');
    }

    public function test_admin_cannot_add_to_cart_or_access_checkout(): void
    {
        $admin = User::create([
            'name' => 'Store Admin',
            'email' => 'storeadmin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $product = Product::create([
            'category_id' => Category::first()->id,
            'name' => 'Server Hardware',
            'slug' => 'server-hardware',
            'description' => 'Enterprise blade server',
            'price' => 2999.99,
            'stock' => 10,
            'image' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45',
        ]);

        $cartAddRes = $this->actingAs($admin)->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
        $cartAddRes->assertSessionHas('warning');

        $checkoutRes = $this->actingAs($admin)->get('/checkout');
        $checkoutRes->assertRedirect('/admin/dashboard');
        $checkoutRes->assertSessionHas('warning');
    }

    public function test_coupon_application_and_discount_calculation(): void
    {
        $response = $this->post('/cart/coupon/apply', [
            'coupon_code' => 'SESHOP2026',
        ]);
        $response->assertSessionHas('success');
        $coupon = session('coupon');
        $this->assertIsArray($coupon);
        $this->assertEquals('SESHOP2026', $coupon['code']);
        $this->assertEquals(20, $coupon['value']);

        $removeRes = $this->get('/cart/coupon/remove');
        $removeRes->assertSessionHas('success');
        $this->assertNull(session('coupon'));
    }

    public function test_stock_validation_blocks_exceeding_quantity(): void
    {
        $product = Product::create([
            'category_id' => Category::first()->id,
            'name' => 'Limited Stock GPU',
            'slug' => 'limited-stock-gpu',
            'description' => 'Graphics card',
            'price' => 999.99,
            'stock' => 2,
            'image' => 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea',
        ]);

        $response = $this->post('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 10,
        ]);
        $response->assertSessionHas('warning');
    }

    public function test_printable_invoice_generation(): void
    {
        $customer = User::create([
            'name'     => 'Jane Smith',
            'email'    => 'jane@example.com',
            'password' => bcrypt('password'),
            'role'     => 'customer',
        ]);

        $order = Order::create([
            'order_number'    => 'SE-ORD-TEST999',
            'user_id'         => $customer->id,
            'customer_name'   => 'Jane Smith',
            'customer_email'  => 'jane@example.com',
            'customer_phone'  => '1234567890',
            'shipping_address'=> '456 Tech Blvd',
            'city'            => 'Austin',
            'postal_code'     => '78701',
            'total_amount'    => 499.99,
            'status'          => 'processing',
        ]);

        // Authenticated owner should see the invoice
        $invoiceRes = $this->actingAs($customer)->get("/order/{$order->order_number}/invoice");
        $invoiceRes->assertStatus(200);
        $invoiceRes->assertSee('INVOICE');
        $invoiceRes->assertSee('SE-ORD-TEST999');
    }

    public function test_unauthenticated_guest_cannot_view_invoice(): void
    {
        $order = Order::create([
            'order_number'     => 'SE-ORD-GUEST001',
            'customer_name'    => 'Guest User',
            'customer_email'   => 'guest@example.com',
            'customer_phone'   => '0000000000',
            'shipping_address' => '1 Guest Ave',
            'city'             => 'Nowhere',
            'postal_code'      => '00000',
            'total_amount'     => 99.99,
            'status'           => 'pending',
        ]);

        // Guest should be redirected to login
        $guestRes = $this->get("/order/{$order->order_number}/invoice");
        $guestRes->assertRedirect('/login');
    }
}
