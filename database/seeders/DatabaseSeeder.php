<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('users')->insert([
            'username' => 'test',
            'email' => 'test@gmail.com',
            'password' => Hash::make('test'), // Use secure password
            'role' => 'Super Admin',
            'session_id' => Str::random(40),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->call(ProductSeeder::class);
    }
}
