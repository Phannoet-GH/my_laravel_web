<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestApiSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Laptops & Workstations',
            'slug' => 'laptops-workstations',
            'description' => 'Developer laptops'
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'SE ProBook Cyber X 16"',
            'slug' => 'se-probook-cyber-x-16',
            'tagline' => 'Next-Gen M3 Ultra Architecture',
            'description' => 'High performance laptop for developers',
            'price' => 2499.99,
            'sale_price' => 2299.99,
            'stock' => 10,
            'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8',
        ]);
    }

    public function test_api_products_list_and_detail(): void
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'data');

        $detailRes = $this->getJson("/api/products/{$this->product->slug}");
        $detailRes->assertStatus(200);
        $detailRes->assertJsonPath('success', true);
        $detailRes->assertJsonPath('data.name', 'SE ProBook Cyber X 16"');
    }

    public function test_api_categories_list_and_detail(): void
    {
        $response = $this->getJson('/api/categories');
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'data');

        $detailRes = $this->getJson("/api/categories/{$this->category->slug}");
        $detailRes->assertStatus(200);
        $detailRes->assertJsonPath('success', true);
        $detailRes->assertJsonPath('data.name', 'Laptops & Workstations');
    }

    public function test_api_user_registration_and_login(): void
    {
        $regRes = $this->postJson('/api/auth/register', [
            'name' => 'API Test User',
            'email' => 'apitest@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $regRes->assertStatus(201);
        $regRes->assertJsonPath('success', true);
        $this->assertNotNull($regRes->json('token'));

        $loginRes = $this->postJson('/api/auth/login', [
            'email' => 'apitest@example.com',
            'password' => 'password123',
        ]);

        $loginRes->assertStatus(200);
        $loginRes->assertJsonPath('success', true);
        $this->assertNotNull($loginRes->json('token'));
    }

    public function test_api_guest_checkout_and_order_lookup(): void
    {
        $checkoutRes = $this->postJson('/api/orders/guest-checkout', [
            'customer_name' => 'Guest API Buyer',
            'customer_email' => 'guestapi@example.com',
            'customer_phone' => '1234567890',
            'shipping_address' => '742 Silicon Valley Ave',
            'city' => 'San Francisco',
            'postal_code' => '94107',
            'payment_method' => 'card',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                ]
            ]
        ]);

        $checkoutRes->assertStatus(201);
        $checkoutRes->assertJsonPath('success', true);
        $orderNumber = $checkoutRes->json('data.order_number');
        $this->assertNotNull($orderNumber);

        // Test order lookup endpoint
        $lookupRes = $this->postJson('/api/orders/lookup', [
            'order_number' => $orderNumber,
            'email' => 'guestapi@example.com',
        ]);

        $lookupRes->assertStatus(200);
        $lookupRes->assertJsonPath('success', true);
        $lookupRes->assertJsonPath('data.customer_name', 'Guest API Buyer');
    }

    public function test_api_product_review_submission(): void
    {
        $reviewRes = $this->postJson("/api/products/{$this->product->id}/reviews", [
            'author_name' => 'Dev Tester',
            'rating' => 5,
            'headline' => 'Superb machine!',
            'comment' => 'Runs all my docker containers without sweating.',
        ]);

        $reviewRes->assertStatus(201);
        $reviewRes->assertJsonPath('success', true);

        $getReviewsRes = $this->getJson("/api/products/{$this->product->id}/reviews");
        $getReviewsRes->assertStatus(200);
        $getReviewsRes->assertJsonCount(1, 'data');
    }
}
