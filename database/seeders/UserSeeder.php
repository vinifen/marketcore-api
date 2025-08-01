<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'role' => UserRole::CLIENT,
            'password' => bcrypt('admin123456'),
        ]);

        User::factory()->create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'role' => UserRole::CLIENT,
            'password' => bcrypt('admin123456'),
        ]);

        User::factory()->create([
            'name' => 'User 3',
            'email' => 'user3@example.com',
            'role' => UserRole::CLIENT,
            'password' => bcrypt('admin123456'),
        ]);

        User::factory()->count(10)->create();
    }
}
