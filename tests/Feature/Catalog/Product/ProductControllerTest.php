<?php

namespace Tests\Feature\Catalog\Product;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_product(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();

        $payload = [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'stock' => 10,
            'price' => 99.99,
        ];

        $response = $this->actingAs($staff)->postJson('/api/products', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'success' => true,
                'name' => 'Test Product',
                'category_id' => $category->id,
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'category_id' => $category->id,
        ]);
    }

    public function test_non_staff_cannot_create_product(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        $category = Category::factory()->create();

        $payload = [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'stock' => 10,
            'price' => 99.99,
        ];

        $response = $this->actingAs($client)->postJson('/api/products', $payload);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_create_product(): void
    {
        $category = Category::factory()->create();

        $payload = [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'stock' => 10,
            'price' => 99.99,
        ];

        $response = $this->postJson('/api/products', $payload);

        $response->assertStatus(401)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_staff_can_update_product(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'name' => 'Old Name']);

        $payload = [
            'name' => 'Updated Name',
            'stock' => 20,
            'price' => 199.99,
        ];

        $response = $this->actingAs($staff)->putJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'name' => 'Updated Name',
                'stock' => 20,
                'price' => 199.99,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'stock' => 20,
            'price' => 199.99,
        ]);
    }

    public function test_non_staff_cannot_update_product(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'name' => 'Old Name']);

        $payload = [
            'name' => 'Updated Name',
        ];

        $response = $this->actingAs($client)->putJson("/api/products/{$product->id}", $payload);

        $response->assertStatus(403);
    }

    public function test_staff_can_soft_delete_product(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($staff)->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_staff_can_restore_soft_deleted_product(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($staff)->deleteJson("/api/products/{$product->id}");

        $response = $this->actingAs($staff)->postJson("/api/products/{$product->id}/restore");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'id' => $product->id,
                'name' => $product->name,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_force_delete_soft_deleted_product(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($admin)->deleteJson("/api/products/{$product->id}");

        $response = $this->actingAs($admin)->deleteJson("/api/products/{$product->id}/force-delete");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_staff_cannot_force_delete_product(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($staff)->deleteJson("/api/products/{$product->id}");

        $response = $this->actingAs($staff)->deleteJson("/api/products/{$product->id}/force-delete");

        $response->assertStatus(403);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_restore_should_fail_if_product_not_soft_deleted(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($staff)->postJson("/api/products/{$product->id}/restore");

        $response->assertStatus(404)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_soft_delete_product_also_soft_deletes_discounts(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount1 = Discount::factory()->create(['product_id' => $product->id]);
        $discount2 = Discount::factory()->create(['product_id' => $product->id]);

        $response = $this->actingAs($staff)->deleteJson("/api/products/{$product->id}");
        $response->assertStatus(204);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->assertSoftDeleted('discounts', ['id' => $discount1->id]);
        $this->assertSoftDeleted('discounts', ['id' => $discount2->id]);
    }

    public function test_restore_product_also_restores_discounts(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id]);

        $this->actingAs($staff)->deleteJson("/api/products/{$product->id}");
        $this->actingAs($staff)->postJson("/api/products/{$product->id}/restore");

        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('discounts', ['id' => $discount->id, 'deleted_at' => null]);
    }
}