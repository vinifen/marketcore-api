<?php

namespace Tests\Feature\Catalog\Discount;

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_discount(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $payload = [
            'product_id' => $product->id,
            'description' => 'Special promotion',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'discount_percentage' => 15.5,
        ];

        $response = $this->actingAs($admin)->postJson('/api/discounts', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'success' => true,
                'product_id' => $product->id,
                'description' => 'Special promotion',
                'discount_percentage' => 15.5,
            ]);

        $this->assertDatabaseHas('discounts', [
            'product_id' => $product->id,
            'description' => 'Special promotion',
        ]);
    }

    public function test_non_admin_cannot_create_discount(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $payload = [
            'product_id' => $product->id,
            'description' => 'Special promotion',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'discount_percentage' => 10,
        ];

        $response = $this->actingAs($moderator)->postJson('/api/discounts', $payload);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_create_discount(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $payload = [
            'product_id' => $product->id,
            'description' => 'Special promotion',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'discount_percentage' => 10,
        ];

        $response = $this->postJson('/api/discounts', $payload);

        $response->assertStatus(401)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_admin_can_update_discount(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id, 'description' => 'Old']);

        $payload = [
            'description' => 'New description',
            'discount_percentage' => 25,
        ];

        $response = $this->actingAs($admin)->putJson("/api/discounts/{$discount->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'description' => 'New description',
                'discount_percentage' => 25,
            ]);

        $this->assertDatabaseHas('discounts', [
            'id' => $discount->id,
            'description' => 'New description',
            'discount_percentage' => 25,
        ]);
    }

    public function test_non_admin_cannot_update_discount(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id]);

        $payload = [
            'description' => 'Update attempt',
        ];

        $response = $this->actingAs($moderator)->putJson("/api/discounts/{$discount->id}", $payload);

        $response->assertStatus(403);
    }

    public function test_admin_can_soft_delete_discount(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id]);

        $response = $this->actingAs($admin)->deleteJson("/api/discounts/{$discount->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted('discounts', ['id' => $discount->id]);
    }

    public function test_non_admin_cannot_delete_discount(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id]);

        $response = $this->actingAs($moderator)->deleteJson("/api/discounts/{$discount->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_restore_soft_deleted_discount(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id]);

        $this->actingAs($admin)->deleteJson("/api/discounts/{$discount->id}");

        $response = $this->actingAs($admin)->postJson("/api/discounts/{$discount->id}/restore");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'id' => $discount->id,
                'description' => $discount->description,
            ]);

        $this->assertDatabaseHas('discounts', [
            'id' => $discount->id,
            'deleted_at' => null,
        ]);
    }

    public function test_restore_should_fail_if_discount_not_soft_deleted(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id]);

        $response = $this->actingAs($admin)->postJson("/api/discounts/{$discount->id}/restore");

        $response->assertStatus(404)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_admin_can_force_delete_soft_deleted_discount(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id]);

        $this->actingAs($admin)->deleteJson("/api/discounts/{$discount->id}");

        $response = $this->actingAs($admin)->deleteJson("/api/discounts/{$discount->id}/force-delete");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('discounts', ['id' => $discount->id]);
    }

    public function test_non_admin_cannot_restore_discount(): void
    {
        $admin = $this->createTestUser(['email' => 'admin@example.com', 'role' => UserRole::ADMIN]);
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id]);

        $this->actingAs($admin)->deleteJson("/api/discounts/{$discount->id}");

        $response = $this->actingAs($moderator)->postJson("/api/discounts/{$discount->id}/restore");

        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_force_delete_discount(): void
    {
        $admin = $this->createTestUser(['email' => 'admin@example.com', 'role' => UserRole::ADMIN]);
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id]);

        $this->actingAs($admin)->deleteJson("/api/discounts/{$discount->id}");

        $response = $this->actingAs($moderator)->deleteJson("/api/discounts/{$discount->id}/force-delete");

        $response->assertStatus(403);
        $this->assertSoftDeleted('discounts', ['id' => $discount->id]);
    }

    public function test_guest_can_list_discounts(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        Discount::factory()->count(2)->create(['product_id' => $product->id]);

        $response = $this->getJson('/api/discounts');

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [['id', 'product_id', 'description', 'start_date', 'end_date', 'discount_percentage']],
            ]);
    }

    public function test_guest_can_view_discount(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id, 'description' => 'Test Discount']);

        $response = $this->getJson("/api/discounts/{$discount->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'description' => 'Test Discount',
            ]);
    }

    public function test_client_can_list_discounts(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        Discount::factory()->count(2)->create(['product_id' => $product->id]);

        $response = $this->actingAs($client)->getJson('/api/discounts');

        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [['id', 'product_id', 'description', 'start_date', 'end_date', 'discount_percentage']],
            ]);
    }

    public function test_client_can_view_discount(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $discount = Discount::factory()->create(['product_id' => $product->id, 'description' => 'Test Discount']);

        $response = $this->actingAs($client)->getJson("/api/discounts/{$discount->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'description' => 'Test Discount',
            ]);
    }
}