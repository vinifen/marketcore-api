<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

class DiscountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => $this->faker->numberBetween(1, 10),
            'description' => $this->faker->optional()->sentence(3),
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'discount_percentage' => $this->faker->randomFloat(2, 1, 50),
        ];
    }
}