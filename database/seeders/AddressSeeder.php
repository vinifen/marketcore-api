<?php

namespace Database\Seeders;

use App\Models\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        Address::factory()->count(3)->create(['user_id' => 1]);
        Address::factory()->count(3)->create(['user_id' => 2]);
        Address::factory()->count(3)->create(['user_id' => 3]);
        Address::factory()->count(3)->create(['user_id' => 4]);
        Address::factory()->count(3)->create(['user_id' => 5]);

        foreach (range(1, 15) as $userId) {
            Address::factory()->create(['user_id' => $userId]);
        }
    }
}
