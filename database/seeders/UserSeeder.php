<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a dummy admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@parampara.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Create another test user
        User::create([
            'name' => 'Test User',
            'email' => 'test@parampara.com',
            'password' => Hash::make('test123456'),
            'email_verified_at' => now(),
        ]);
    }
}
