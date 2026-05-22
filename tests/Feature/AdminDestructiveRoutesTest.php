<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AdminDestructiveRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::create([
            'username'   => 'admin',
            'email'      => 'admin@test.com',
            'password'   => bcrypt('password'),
            'role'       => 'Super Admin',
            'is_deleted' => 0,
        ]);
    }

    public function test_product_delete_rejects_get(): void
    {
        $this->get('/admin/product-delete?id=1')->assertStatus(405);
    }

    public function test_product_restore_rejects_get(): void
    {
        $this->get('/admin/product-restore?id=1')->assertStatus(405);
    }

    public function test_warranty_delete_rejects_get(): void
    {
        $this->get('/admin/warranty-delete?id=1')->assertStatus(405);
    }

    public function test_warranty_restore_rejects_get(): void
    {
        $this->get('/admin/warranty-restore?id=1')->assertStatus(405);
    }

    public function test_user_delete_rejects_get(): void
    {
        $this->get('/admin/user-delete?id=1')->assertStatus(405);
    }

    public function test_user_restore_rejects_get(): void
    {
        $this->get('/admin/user-restore?id=1')->assertStatus(405);
    }

    public function test_product_name_delete_rejects_get(): void
    {
        $this->get('/admin/product-name-delete?id=1')->assertStatus(405);
    }

    public function test_warranty_change_status_rejects_invalid_status(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin)
             ->postJson('/admin/warranty-status-change', ['id' => 1, 'status' => 'HACKED'])
             ->assertStatus(422);
    }

    public function test_warranty_change_status_accepts_valid_status(): void
    {
        $admin = $this->makeAdmin();
        $warranty = \App\Models\Warranty::create([
            'user_name'       => 'Test',
            'mobile_number'   => '9876543210',
            'purchase_source' => 'Online',
            'product_name'    => 'Product',
            'serial_number'   => 'SN-001',
            'purchase_date'   => '2024-01-01',
            'expiry_date'     => '2025-01-01',
            'warranty_status' => 'Pending',
            'is_deleted'      => 0,
        ]);

        $this->actingAs($admin)
             ->postJson('/admin/warranty-status-change', ['id' => $warranty->id, 'status' => 'Active'])
             ->assertStatus(200)
             ->assertJson(['status' => 1]);

        $this->assertDatabaseHas('warranty_records', ['id' => $warranty->id, 'warranty_status' => 'Active']);
    }
}
