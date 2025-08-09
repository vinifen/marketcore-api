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
        $start = $this->faker->dateTimeBetween('-7 days', '+7 days');
        $end = $this->faker->dateTimeBetween($start, '+90 days');

        return [
            'code' => $this->faker->unique()->bothify('COUPON-####'),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'discount_percentage' => $this->faker->randomFloat(2, 1, 99),
        ];
    }
}
