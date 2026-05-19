<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kingster.com'],
            [
                'username' => 'admin',
                'email' => 'admin@kingster.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_by' => 1,
                'modified_by' => 1,
                'is_deleted' => 0,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@kingster.com'],
            [
                'username' => 'staff',
                'email' => 'staff@kingster.com',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'created_by' => 1,
                'modified_by' => 1,
                'is_deleted' => 0,
            ]
        );

        $this->command->info('Test users created:');
        $this->command->info('Admin: admin@kingster.com / password');
        $this->command->info('Staff: staff@kingster.com / password');

        DB::table('users')->where('email', 'test@gmail.com')->update([
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        
        $this->command->info('Test user updated: test@gmail.com / password');
    }
}
