<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'superadmin',
            'full_name' => 'Super Administrator',
            'email' => 'superadmin@example.com',
            'phone_number' => '081234567890',
            'address' => 'Jl. Admin No.1',
            'profile_picture' => null,
            'password' => 'password123',
            'role' => 'super_admin',
        ]);

        User::create([
            'username' => 'user1',
            'full_name' => 'User Satu',
            'email' => 'user1@example.com',
            'phone_number' => '081234567891',
            'address' => 'Jl. User No.1',
            'profile_picture' => null,
            'password' => 'userpass',
            'role' => 'user',
        ]);

        User::create([
            'username' => 'user2',
            'full_name' => 'User Dua',
            'email' => 'user2@example.com',
            'phone_number' => '081234567892',
            'address' => 'Jl. User No.2',
            'profile_picture' => null,
            'password' => 'userpass',
            'role' => 'user',
        ]);
    }
}
