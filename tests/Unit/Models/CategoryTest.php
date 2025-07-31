<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_category(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'description' => 'A test description',
        ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Test Category',
            'description' => 'A test description',
        ]);
    }

    public function test_fillable_attributes(): void
    {
        $category = new Category();

        $this->assertEquals([
            'name',
            'description',
        ], $category->getFillable());
    }
}
