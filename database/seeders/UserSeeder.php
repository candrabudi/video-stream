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
            'password' => 'password123',
            'role' => 'super_admin',
        ]);

        User::create([
            'username' => 'user1',
            'password' => 'userpass',
            'role' => 'user',
        ]);
    }
}
