<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('COUPON-####'),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'discount_percentage' => $this->faker->randomFloat(2, 1, 99),
        ];
    }
}
