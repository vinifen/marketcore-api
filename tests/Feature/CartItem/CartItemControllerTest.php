<?php

namespace Tests\Feature\CartItem;

use App\Enums\UserRole;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartItemControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_cart_item(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 99.99, 'category_id' => $category->id]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ];

        $response = $this->actingAs($user)->postJson('/api/cart-items', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => 2,
            ]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_client_cannot_create_cart_item_with_insufficient_stock(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['stock' => 1, 'category_id' => $category->id]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ];

        $response = $this->actingAs($user)->postJson('/api/cart-items', $payload);

        $response->assertStatus(422)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_client_can_update_cart_item_quantity(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 50, 'category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $payload = [
            'quantity' => 5,
        ];

        $response = $this->actingAs($user)->putJson("/api/cart-items/{$cartItem->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'quantity' => 5,
            ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    public function test_client_can_view_cart_item(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['name' => 'Test Product', 'category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->getJson("/api/cart-items/{$cartItem->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $cartItem->id,
                'product_name' => 'Test Product',
                'user_id' => $user->id,
            ]);
    }

    public function test_client_can_delete_cart_item(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/cart-items/{$cartItem->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    public function test_client_cannot_access_other_users_cart_item(): void
    {
        $user = $this->createTestUser();
        $otherUser = $this->createTestUser(['email' => 'other@example.com']);
        $cart = Cart::firstOrCreate(['user_id' => $otherUser->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/cart-items/{$cartItem->id}");

        $response->assertStatus(403);
    }

    public function test_guest_cannot_create_cart_item(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ];

        $response = $this->postJson('/api/cart-items', $payload);

        $response->assertStatus(401);
    }

    public function test_admin_can_create_cart_item_for_other_user(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN, 'email' => 'admin@example.com']);
        $otherUser = $this->createTestUser(['email' => 'otheruser@example.com']);
        $cart = Cart::firstOrCreate(['user_id' => $otherUser->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 123.45, 'category_id' => $category->id]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ];

        $response = $this->actingAs($admin)->postJson('/api/cart-items', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => 3,
                'user_id' => $otherUser->id,
            ]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_moderator_can_list_cart_items(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR, 'email' => 'moderator@example.com']);
        $user = $this->createTestUser(['email' => 'client@example.com']);
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($moderator)->getJson('/api/cart-items');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $cartItem->id,
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => 2,
            ]);
    }

    public function test_moderator_cannot_create_or_edit_cart_item_for_other_user(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR, 'email' => 'moderator@example.com']);
        $user = $this->createTestUser(['email' => 'client@example.com']);
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ];
        $createResponse = $this->actingAs($moderator)->postJson('/api/cart-items', $payload);
        $createResponse->assertStatus(403);

        $updatePayload = [
            'quantity' => 5,
        ];
        $updateResponse = $this->actingAs($moderator)->putJson("/api/cart-items/{$cartItem->id}", $updatePayload);
        $updateResponse->assertStatus(403);
    }

    public function test_user_cannot_create_or_edit_cart_item_for_other_user(): void
    {
        $user = $this->createTestUser(['email' => 'user1@example.com']);
        $otherUser = $this->createTestUser(['email' => 'user2@example.com']);
        $cart = Cart::firstOrCreate(['user_id' => $otherUser->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ];
        $createResponse = $this->actingAs($user)->postJson('/api/cart-items', $payload);
        $createResponse->assertStatus(403);

        $updatePayload = [
            'quantity' => 5,
        ];
        $updateResponse = $this->actingAs($user)->putJson("/api/cart-items/{$cartItem->id}", $updatePayload);
        $updateResponse->assertStatus(403);
    }

    public function test_cannot_create_cart_item_with_invalid_product(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => 99999,
            'quantity' => 1,
        ];

        $response = $this->actingAs($user)->postJson('/api/cart-items', $payload);

        $response->assertStatus(422)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_cannot_update_cart_item_with_insufficient_stock(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['stock' => 2, 'price' => 50, 'category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $payload = [
            'quantity' => 10,
        ];

        $response = $this->actingAs($user)->putJson("/api/cart-items/{$cartItem->id}", $payload);

        $response->assertStatus(422)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_cannot_create_cart_item_with_negative_quantity(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'category_id' => $category->id]);

        $payload = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => -1,
        ];

        $response = $this->actingAs($user)->postJson('/api/cart-items', $payload);

        $response->assertStatus(422)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_cannot_update_cart_item_to_zero_quantity(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $payload = [
            'quantity' => 0,
        ];

        $response = $this->actingAs($user)->putJson("/api/cart-items/{$cartItem->id}", $payload);

        $response->assertStatus(422)
            ->assertJsonFragment(['success' => false]);
    }

    public function test_client_can_remove_one_cart_item(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 99.99, 'category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/cart-items/{$cartItem->id}/remove-one");

        $response->assertStatus(204);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 1,
        ]);
    }

    public function test_remove_one_decrements_quantity_and_returns_204(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/cart-items/{$cartItem->id}/remove-one");

        $response->assertStatus(204);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 2,
        ]);
    }

    public function test_remove_one_deletes_cart_item_when_quantity_is_one(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/cart-items/{$cartItem->id}/remove-one");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    public function test_delete_cart_item_removes_from_database_and_returns_204(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/cart-items/{$cartItem->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    public function test_update_cart_item_quantity_success(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $payload = ['quantity' => 3];

        $response = $this->actingAs($user)->putJson("/api/cart-items/{$cartItem->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['quantity' => 3]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 3,
        ]);
    }

    public function test_update_cart_item_quantity_invalid_returns_422(): void
    {
        $user = $this->createTestUser();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'category_id' => $category->id]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $payload = ['quantity' => 10]; // exceeds stock

        $response = $this->actingAs($user)->putJson("/api/cart-items/{$cartItem->id}", $payload);

        $response->assertStatus(422)
            ->assertJsonFragment(['success' => false]);
    }
}
