<?php

namespace Tests\Feature\Catalog\Category;

use App\Enums\UserRole;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_category(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->postJson('/api/categories', [
            'name' => 'Electronics',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'success' => true,
                'name' => 'Electronics',
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
        ]);
    }

    public function test_non_admin_cannot_create_category(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);

        $response = $this->actingAs($moderator)->postJson('/api/categories', [
            'name' => 'Books',
        ]);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_create_category(): void
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Books',
        ]);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'success' => false,
            ]);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->putJson("/api/categories/{$category->id}", [
            'name' => 'New Name',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'name' => 'New Name',
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
        ]);
    }

    public function test_non_admin_cannot_update_category(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($moderator)->putJson("/api/categories/{$category->id}", [
            'name' => 'New Name',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_non_admin_cannot_delete_category(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $category = Category::factory()->create();

        $response = $this->actingAs($moderator)->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_view_category(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $category = Category::factory()->create(['name' => 'TestCat']);

        $response = $this->actingAs($admin)->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
                'name' => 'TestCat',
            ]);
    }

    public function test_staff_can_list_categories(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        Category::factory()->count(2)->create();

        $response = $this->actingAs($moderator)->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [['id', 'name']],
            ]);
    }

    public function test_non_staff_cannot_list_categories(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);

        $response = $this->actingAs($client)->getJson('/api/categories');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_list_categories(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(401)
            ->assertJsonFragment([
                'success' => false,
            ]);
    }
}
