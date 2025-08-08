<?php

namespace Tests\Unit\Models;

use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    public function test_coupon_can_be_created_with_factory(): void
    {
        $coupon = Coupon::factory()->create([
            'code' => 'TESTCODE',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'discount_percentage' => 10.5,
        ]);

        $this->assertInstanceOf(Coupon::class, $coupon);
        $this->assertEquals('TESTCODE', $coupon->code);
        $this->assertEquals(10.5, $coupon->discount_percentage);
    }

    public function test_fillable_attributes(): void
    {
        $coupon = new Coupon();

        $this->assertEquals([
            'code',
            'start_date',
            'end_date',
            'discount_percentage',
        ], $coupon->getFillable());
    }

    public function test_casts_attributes(): void
    {
        $coupon = new Coupon();

        $this->assertEquals([
            'id' => 'integer',
            'code' => 'string',
            'start_date' => 'date',
            'end_date' => 'date',
            'discount_percentage' => 'float',
            'deleted_at' => 'datetime',
        ], $coupon->getCasts());
    }
}
