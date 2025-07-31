<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_discount(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        /** @var \App\Models\Discount $discount */
        $discount = Discount::factory()->create([
            'product_id' => $product->id,
            'description' => 'Desconto Teste',
            'startDate' => now()->toDateString(),
            'endDate' => now()->addDays(5)->toDateString(),
            'discountPercentage' => 20.5,
        ]);

        $this->assertDatabaseHas('discounts', [
            'id' => $discount->id,
            'product_id' => $product->id,
            'description' => 'Desconto Teste',
            'discountPercentage' => 20.5,
        ]);
    }

    public function test_it_casts_fields_correctly(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        /** @var \App\Models\Discount $discount */
        $discount = Discount::factory()->create([
            'product_id' => $product->id,
            'discountPercentage' => '15.75',
            'startDate' => '2025-01-01',
            'endDate' => '2025-01-10',
        ]);

        $this->assertSame(15.75, $discount->discountPercentage);
        $this->assertEquals('2025-01-01', Carbon::parse($discount->startDate)->format('Y-m-d'));
        $this->assertEquals('2025-01-10', Carbon::parse($discount->endDate)->format('Y-m-d'));
    }

    public function test_it_belongs_to_product(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        /** @var \App\Models\Discount $discount */
        $discount = Discount::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $discount->product);
        $this->assertEquals($product->id, $discount->product->id);
    }

    public function test_fillable_attributes(): void
    {
        $discount = new Discount();

        $this->assertEquals([
            'product_id',
            'description',
            'startDate',
            'endDate',
            'discountPercentage',
        ], $discount->getFillable());
    }
}
