<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class DiscountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'description' => $this->faker->sentence(3),
            'startDate' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'endDate' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'discountPercentage' => $this->faker->randomFloat(2, 1, 50),
        ];
    }
}