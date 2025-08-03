<?php

namespace Tests\Feature\Cart;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_is_created_when_user_is_created(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
        ]);

        $cart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($cart);
        $this->assertEquals($user->id, $cart->user_id);
    }

    public function test_cart_is_deleted_when_user_is_deleted(): void
    {
        $user = User::factory()->create();
        $cartId = $user->cart->id ?? Cart::where('user_id', $user->id)->value('id');

        $user->delete();

        $this->assertDatabaseMissing('carts', [
            'id' => $cartId,
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_view_own_cart(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/cart/{$cart->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $cart->id,
                'user_id' => $user->id,
            ]);
    }

    public function test_authenticated_user_cannot_view_other_users_cart(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherCart = $otherUser->cart ?? Cart::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->getJson("/api/cart/{$otherCart->id}");

        $response->assertStatus(403);
    }

    public function test_guest_cannot_view_any_cart(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/cart/{$cart->id}");

        $response->assertStatus(401);
    }

    public function test_admin_can_list_all_carts(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $cart1 = $user1->cart ?? Cart::factory()->create(['user_id' => $user1->id]);
        $cart2 = $user2->cart ?? Cart::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($admin)->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $cart1->id])
            ->assertJsonFragment(['id' => $cart2->id]);
    }

    public function test_non_admin_cannot_list_all_carts(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/cart');
        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_clear_own_cart(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);
        $cart->refresh();

        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $cart->items()->createMany([
            ['product_id' => $product1->id, 'quantity' => 2, 'unit_price' => 10.0],
            ['product_id' => $product2->id, 'quantity' => 1, 'unit_price' => 5.0],
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/cart/{$cart->id}/clear");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Cart cleared successfully']);

        $this->assertCount(0, $cart->items()->get());
    }

    public function test_authenticated_user_cannot_clear_other_users_cart(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherCart = $otherUser->cart ?? Cart::factory()->create(['user_id' => $otherUser->id]);
        $otherCart->refresh();

        $product = Product::factory()->create();

        $otherCart->items()->create(['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 10.0]);

        $response = $this->actingAs($user)->deleteJson("/api/cart/{$otherCart->id}/clear");

        $response->assertStatus(403);
        $this->assertCount(1, $otherCart->items()->get());
    }

    public function test_admin_can_clear_any_cart(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $user = User::factory()->create();
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);
        $cart->refresh();

        $product = Product::factory()->create();

        $cart->items()->create(['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 10.0]);

        $response = $this->actingAs($admin)->deleteJson("/api/cart/{$cart->id}/clear");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Cart cleared successfully']);

        $this->assertCount(0, $cart->items()->get());
    }

    public function test_guest_cannot_clear_cart(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);
        $cart->refresh();

        $product = Product::factory()->create();

        $cart->items()->create(['product_id' => $product->id, 'quantity' => 2, 'unit_price' => 10.0]);

        $response = $this->deleteJson("/api/cart/{$cart->id}/clear");

        $response->assertStatus(401);
        $this->assertCount(1, $cart->items()->get());
    }
}
