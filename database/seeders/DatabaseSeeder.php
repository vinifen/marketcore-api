<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserStaffSeeder::class,
            UserSeeder::class,
            AddressSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            DiscountSeeder::class,
        ]);
    }
}
