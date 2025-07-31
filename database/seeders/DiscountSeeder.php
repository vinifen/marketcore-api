<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Discount;
use App\Models\Product;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::factory()->count(23)->create();

        foreach ($products->take(3) as $product) {
            Discount::factory()->count(2)->create([
                'product_id' => $product->id,
            ]);
        }

        foreach ($products->slice(3, 20) as $product) {
            Discount::factory()->create([
                'product_id' => $product->id,
            ]);
        }
    }
}