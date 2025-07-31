<?php

namespace Tests\Feature\Catalog\Product;

use App\Enums\UserRole;
use App\Models\Category;
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

    public function test_staff_can_delete_product(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($staff)->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_non_staff_cannot_delete_product(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($client)->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_view_product(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create(['name' => 'Cat']);
        $product = Product::factory()->create(['category_id' => $category->id, 'name' => 'TestProd']);

        $response = $this->actingAs($admin)->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'name' => 'TestProd',
                'category' => 'Cat',
            ]);
    }

    public function test_guest_can_view_product(): void
    {
        $category = Category::factory()->create(['name' => 'Cat']);
        $product = Product::factory()->create(['category_id' => $category->id, 'name' => 'TestProd']);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'name' => 'TestProd',
                'category' => 'Cat',
            ]);
    }

    public function test_client_can_view_product(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        $category = Category::factory()->create(['name' => 'Cat']);
        $product = Product::factory()->create(['category_id' => $category->id, 'name' => 'TestProd']);

        $response = $this->actingAs($client)->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'name' => 'TestProd',
                'category' => 'Cat',
            ]);
    }

    public function test_staff_can_list_products(): void
    {
        $staff = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        Product::factory()->count(2)->create(['category_id' => $category->id]);

        $response = $this->actingAs($staff)->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [['id', 'category_id', 'name', 'category']],
            ]);
    }

    public function test_guest_can_list_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(2)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [['id', 'category_id', 'name', 'category']],
            ]);
    }

    public function test_client_can_list_products(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        $category = Category::factory()->create();
        Product::factory()->count(2)->create(['category_id' => $category->id]);

        $response = $this->actingAs($client)->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [['id', 'category_id', 'name', 'category']],
            ]);
    }
}
