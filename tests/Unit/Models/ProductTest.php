<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_product(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'stock' => 5,
            'price' => 123.45,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Test Product',
            'stock' => 5,
            'price' => 123.45,
        ]);
    }

    public function test_it_casts_fields_correctly(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'stock' => '10',
            'price' => '99.99',
        ]);

        $this->assertSame(10, $product->stock);
        $this->assertSame('99.99', $product->price);
    }

    public function test_it_belongs_to_category(): void
    {
        $category = Category::factory()->create(['name' => 'Cat']);
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals('Cat', $product->category->name);
    }
}
