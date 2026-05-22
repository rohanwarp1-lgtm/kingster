<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeCeo(): User
    {
        return User::create([
            'username'   => 'ceo',
            'email'      => 'ceo@test.com',
            'password'   => bcrypt('password'),
            'role'       => 'CEO',
            'is_deleted' => 0,
        ]);
    }

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

    public function test_ceo_cannot_be_deleted_regardless_of_id(): void
    {
        $admin = $this->makeAdmin();
        $ceo   = $this->makeCeo();

        $this->actingAs($admin)
             ->get('/admin/user-delete?id=' . $ceo->id)
             ->assertStatus(405);
    }

    public function test_ceo_cannot_be_deleted_via_post(): void
    {
        $admin = $this->makeAdmin();
        $ceo   = $this->makeCeo();

        $this->actingAs($admin)
             ->postJson('/admin/user-delete', ['id' => $ceo->id])
             ->assertJson(['status' => false]);

        $this->assertDatabaseHas('users', ['id' => $ceo->id, 'is_deleted' => 0]);
    }

    public function test_regular_user_can_be_deleted(): void
    {
        $admin = $this->makeAdmin();
        $regular = User::create([
            'username'   => 'staff',
            'email'      => 'staff@test.com',
            'password'   => bcrypt('password'),
            'role'       => 'Sub Admin',
            'is_deleted' => 0,
        ]);

        $this->actingAs($admin)
             ->postJson('/admin/user-delete', ['id' => $regular->id])
             ->assertJson(['status' => 1]);

        $this->assertDatabaseHas('users', ['id' => $regular->id, 'is_deleted' => 1]);
    }
}
