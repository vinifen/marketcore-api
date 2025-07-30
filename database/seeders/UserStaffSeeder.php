<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserStaffSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@email.com',
            'role' => UserRole::ADMIN,
            'password' => bcrypt('admin123456'),
        ]);

        User::factory()->create([
            'name' => 'Moderator 1 User',
            'email' => 'mod1@email.com',
            'role' => UserRole::MODERATOR,
            'password' => bcrypt('mod123456'),
        ]);

        User::factory()->create([
            'name' => 'Moderator 2 User',
            'email' => 'mod2@email.com',
            'role' => UserRole::MODERATOR,
            'password' => bcrypt('mod123456'),
        ]);

        User::factory()->create([
            'name' => 'Moderator 3 User',
            'email' => 'mod3@email.com',
            'role' => UserRole::MODERATOR,
            'password' => bcrypt('mod123456'),
        ]);
    }
}
