<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Category for testing order relationships
        Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'description' => 'Workstation hardware'
        ]);

        $this->admin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('AdminPass123!'),
            'role'     => 'admin',
        ]);

        $this->customer = User::create([
            'name'     => 'Jane Doe',
            'email'    => 'jane@example.com',
            'phone'    => '1234567890',
            'password' => Hash::make('CustomerPass123!'),
            'role'     => 'customer',
        ]);
    }

    public function test_guest_or_customer_cannot_access_user_management_endpoints(): void
    {
        // 1. Guest
        $this->get('/admin/users/' . $this->customer->id . '/details')
            ->assertRedirect('/login');

        $this->post('/admin/users', [
            'name'     => 'Hacker',
            'email'    => 'hacker@example.com',
            'password' => 'password123',
            'role'     => 'admin',
        ])->assertRedirect('/login');

        // 2. Customer
        $this->actingAs($this->customer);

        $this->get('/admin/users/' . $this->customer->id . '/details')
            ->assertRedirect('/login')
            ->assertSessionHas('error');

        $this->post('/admin/users', [
            'name'     => 'New Person',
            'email'    => 'new@example.com',
            'password' => 'password123',
            'role'     => 'customer',
        ])->assertRedirect('/login');

        $this->put('/admin/users/' . $this->customer->id, [
            'name'  => 'Updated Person',
            'email' => 'jane@example.com',
            'role'  => 'customer',
        ])->assertRedirect('/login');

        $this->patch('/admin/users/' . $this->customer->id . '/role', [
            'role' => 'admin',
        ])->assertRedirect('/login');

        $this->patch('/admin/users/' . $this->customer->id . '/password', [
            'password'              => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect('/login');

        $this->delete('/admin/users/' . $this->customer->id)
            ->assertRedirect('/login');
    }

    public function test_admin_can_view_users_list_and_search(): void
    {
        $this->actingAs($this->admin);

        // Access dashboard
        $res = $this->get('/admin/dashboard?tab=users');
        $res->assertStatus(200);
        $res->assertSee('Registered Accounts');
        $res->assertSee('Jane Doe');
        $res->assertSee('Super Admin');

        // Search by query
        $searchRes = $this->get('/admin/dashboard?tab=users&user_q=Jane');
        $searchRes->assertStatus(200);
        $searchRes->assertSee('Jane Doe');

        // Filter by role
        $filterRes = $this->get('/admin/dashboard?tab=users&user_role=customer');
        $filterRes->assertStatus(200);
        $filterRes->assertSee('Jane Doe');
    }

    public function test_admin_can_create_new_registered_user(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/users', [
            'name'     => 'Alice Smith',
            'email'    => 'alice@example.com',
            'phone'    => '+1 555 999 8888',
            'password' => 'AliceSecret99!',
            'role'     => 'customer',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $created = User::where('email', 'alice@example.com')->first();
        $this->assertNotNull($created);
        $this->assertEquals('Alice Smith', $created->name);
        $this->assertEquals('customer', $created->role);
        $this->assertTrue(Hash::check('AliceSecret99!', $created->password));
    }

    public function test_admin_can_create_new_admin_user(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post('/admin/users', [
            'name'     => 'Secondary Admin',
            'email'    => 'admin2@example.com',
            'password' => 'SecureAdminPass1!',
            'role'     => 'admin',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $created = User::where('email', 'admin2@example.com')->first();
        $this->assertNotNull($created);
        $this->assertEquals('admin', $created->role);
    }

    public function test_admin_can_update_user_profile(): void
    {
        $this->actingAs($this->admin);

        $response = $this->put('/admin/users/' . $this->customer->id, [
            'name'  => 'Jane Updated',
            'email' => 'jane.updated@example.com',
            'phone' => '9876543210',
            'role'  => 'customer',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->customer->refresh();
        $this->assertEquals('Jane Updated', $this->customer->name);
        $this->assertEquals('jane.updated@example.com', $this->customer->email);
        $this->assertEquals('9876543210', $this->customer->phone);
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $this->actingAs($this->admin);

        // Try to demote via updateUser
        $response = $this->put('/admin/users/' . $this->admin->id, [
            'name'  => 'Super Admin',
            'email' => 'admin@example.com',
            'role'  => 'customer',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'You cannot remove admin privileges from your own active account.');
        $this->admin->refresh();
        $this->assertEquals('admin', $this->admin->role);

        // Try to demote via updateUserRole
        $roleRes = $this->patch('/admin/users/' . $this->admin->id . '/role', [
            'role' => 'customer',
        ]);
        $roleRes->assertRedirect();
        $roleRes->assertSessionHas('error', 'You cannot remove admin privileges from your own account.');
        $this->admin->refresh();
        $this->assertEquals('admin', $this->admin->role);
    }

    public function test_admin_cannot_demote_the_last_remaining_admin(): void
    {
        // Currently $this->admin is the only admin
        $secondAdmin = User::create([
            'name'     => 'Second Admin',
            'email'    => 'second@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // Acting as second admin, try to demote the first admin -> succeeds because 2 admins exist
        $this->actingAs($secondAdmin);
        $this->patch('/admin/users/' . $this->admin->id . '/role', [
            'role' => 'customer',
        ])->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertEquals('customer', $this->admin->role);

        // Now only secondAdmin is admin. Attempting to demote secondAdmin by anyone should fail
        $res = $this->put('/admin/users/' . $secondAdmin->id, [
            'name'  => 'Second Admin',
            'email' => 'second@example.com',
            'role'  => 'customer',
        ]);
        $res->assertSessionHas('error');
        $secondAdmin->refresh();
        $this->assertEquals('admin', $secondAdmin->role);
    }

    public function test_admin_can_update_another_user_role(): void
    {
        $this->actingAs($this->admin);

        $response = $this->patch('/admin/users/' . $this->customer->id . '/role', [
            'role' => 'admin',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->customer->refresh();
        $this->assertEquals('admin', $this->customer->role);
    }

    public function test_admin_can_reset_user_password(): void
    {
        $this->actingAs($this->admin);

        $response = $this->patch('/admin/users/' . $this->customer->id . '/password', [
            'password'              => 'BrandNewPassword123!',
            'password_confirmation' => 'BrandNewPassword123!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->customer->refresh();
        $this->assertTrue(Hash::check('BrandNewPassword123!', $this->customer->password));
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $this->actingAs($this->admin);

        $response = $this->delete('/admin/users/' . $this->admin->id);
        $response->assertRedirect();
        $response->assertSessionHas('error', 'You cannot delete your own active administrator account.');

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_cannot_delete_last_remaining_admin(): void
    {
        // Create another user and make them admin to test
        $otherAdmin = User::create([
            'name'     => 'Other Admin',
            'email'    => 'other@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // Delete otherAdmin while $this->admin is logged in -> succeeds
        $this->actingAs($this->admin);
        $this->delete('/admin/users/' . $otherAdmin->id)
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $otherAdmin->id]);

        // Now only $this->admin remains. Trying to delete will trigger self-delete check or last-admin check
        $this->delete('/admin/users/' . $this->admin->id)
            ->assertSessionHas('error');
    }

    public function test_admin_can_delete_user_and_preserves_order_transactions(): void
    {
        $this->actingAs($this->admin);

        // Create an order associated with customer
        $order = Order::create([
            'order_number'    => 'ORD-TEST-999999',
            'user_id'         => $this->customer->id,
            'customer_name'   => $this->customer->name,
            'customer_email'  => $this->customer->email,
            'customer_phone'  => '1234567890',
            'shipping_address'=> '123 Test St',
            'city'            => 'Tech City',
            'postal_code'     => '90210',
            'payment_method'  => 'cod',
            'subtotal'        => 150.00,
            'tax'             => 15.00,
            'shipping'        => 0.00,
            'discount'        => 0.00,
            'total_amount'    => 165.00,
            'status'          => 'completed',
        ]);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'user_id' => $this->customer->id]);

        // Delete user
        $response = $this->delete('/admin/users/' . $this->customer->id);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // User should be deleted
        $this->assertDatabaseMissing('users', ['id' => $this->customer->id]);

        // Order MUST still exist with user_id null to preserve transaction and platform revenue
        $order->refresh();
        $this->assertNotNull($order);
        $this->assertNull($order->user_id);
        $this->assertEquals(165.00, $order->total_amount);
    }

    public function test_admin_can_fetch_user_details_json(): void
    {
        $this->actingAs($this->admin);

        // Create an order for the user
        Order::create([
            'order_number'    => 'ORD-DETAIL-1234',
            'user_id'         => $this->customer->id,
            'customer_name'   => $this->customer->name,
            'customer_email'  => $this->customer->email,
            'customer_phone'  => '1234567890',
            'shipping_address'=> '456 Cyber Way',
            'city'            => 'Tech City',
            'postal_code'     => '90210',
            'payment_method'  => 'cod',
            'subtotal'        => 200.00,
            'tax'             => 20.00,
            'shipping'        => 0.00,
            'discount'        => 0.00,
            'total_amount'    => 220.00,
            'status'          => 'delivered',
        ]);

        $response = $this->getJson('/admin/users/' . $this->customer->id . '/details');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'user'    => [
                'id'           => $this->customer->id,
                'email'        => $this->customer->email,
                'orders_count' => 1,
            ]
        ]);
        $response->assertJsonFragment([
            'order_number' => 'ORD-DETAIL-1234'
        ]);
    }

    public function test_sanctum_api_admin_users_crud_and_rbac(): void
    {
        // 1. Customer token cannot access API
        Sanctum::actingAs($this->customer);
        $this->getJson('/api/admin/users')->assertStatus(403);

        // 2. Admin token can list users
        Sanctum::actingAs($this->admin);
        $listRes = $this->getJson('/api/admin/users');
        $listRes->assertStatus(200);
        $listRes->assertJsonStructure([
            'success',
            'metrics' => ['total_users', 'customers_count', 'admins_count'],
            'data',
        ]);

        // 3. Admin can create user via API
        $createRes = $this->postJson('/api/admin/users', [
            'name'     => 'API User',
            'email'    => 'api.user@example.com',
            'password' => 'ApiSecretPass123!',
            'role'     => 'customer',
        ]);
        $createRes->assertStatus(201);
        $createdId = $createRes->json('data.id');

        // 4. Admin can view user via API
        $this->getJson("/api/admin/users/{$createdId}")
            ->assertStatus(200)
            ->assertJsonPath('data.user.name', 'API User');

        // 5. Admin can update user via API
        $this->putJson("/api/admin/users/{$createdId}", [
            'name'  => 'API User Updated',
            'email' => 'api.user.updated@example.com',
            'role'  => 'customer',
        ])->assertStatus(200)->assertJsonPath('data.name', 'API User Updated');

        // 6. Admin can reset password via API
        $this->patchJson("/api/admin/users/{$createdId}/password", [
            'password'              => 'NewApiPassword99!',
            'password_confirmation' => 'NewApiPassword99!',
        ])->assertStatus(200);

        // 7. Admin cannot demote themselves via API
        $demoteSelfRes = $this->putJson("/api/admin/users/{$this->admin->id}", [
            'name'  => 'Super Admin',
            'email' => $this->admin->email,
            'role'  => 'customer',
        ]);
        $demoteSelfRes->assertStatus(403);

        // 8. Admin cannot delete themselves via API
        $deleteSelfRes = $this->deleteJson("/api/admin/users/{$this->admin->id}");
        $deleteSelfRes->assertStatus(403);

        // 9. Admin can delete API user
        $this->deleteJson("/api/admin/users/{$createdId}")
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('users', ['id' => $createdId]);
    }
}
