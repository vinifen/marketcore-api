<?php

namespace Tests\Feature\User;

use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CascadeSoftDeleteUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_soft_delete_user_also_soft_deletes_addresses(): void
    {
        $user = $this->createTestUser();
        $address1 = Address::factory()->create(['user_id' => $user->id]);
        $address2 = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(200);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertSoftDeleted('addresses', ['id' => $address1->id]);
        $this->assertSoftDeleted('addresses', ['id' => $address2->id]);
    }

    public function test_restore_user_also_restores_addresses(): void
    {
        $admin = $this->createTestUser(['email' => 'admin@email.com', 'role' => UserRole::ADMIN]);
        $user = $this->createTestUser();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response = $this->actingAs($admin)->postJson("api/users/{$user->id}/restore");
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('addresses', ['id' => $address->id, 'deleted_at' => null]);
    }

    public function test_soft_delete_user_also_soft_deletes_cart_and_cart_items(): void
    {
        $user = $this->createTestUser();
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);

        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $cartItem1 = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
        ]);
        $cartItem2 = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(200);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertSoftDeleted('carts', ['id' => $cart->id]);
        $this->assertSoftDeleted('cart_items', ['id' => $cartItem1->id]);
        $this->assertSoftDeleted('cart_items', ['id' => $cartItem2->id]);
    }

    public function test_restore_user_also_restores_cart_and_cart_items(): void
    {
        $admin = $this->createTestUser(['email' => 'admin-cart@email.com', 'role' => UserRole::ADMIN]);
        $user = $this->createTestUser(['email' => 'cart-user@email.com']);
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);

        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response = $this->actingAs($admin)->postJson("api/users/{$user->id}/restore");
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('carts', ['id' => $cart->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('cart_items', ['id' => $cartItem->id, 'deleted_at' => null]);
    }

    public function test_force_delete_user_also_force_deletes_addresses(): void
    {
        $admin = $this->createTestUser(['email' => 'admin-force@email.com', 'role' => UserRole::ADMIN]);
        $user = $this->createTestUser(['email' => 'user-force@email.com']);
        $address1 = Address::factory()->create(['user_id' => $user->id]);
        $address2 = Address::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response = $this->actingAs($admin)->deleteJson("api/users/{$user->id}/force-delete");
        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('addresses', ['id' => $address1->id]);
        $this->assertDatabaseMissing('addresses', ['id' => $address2->id]);
    }

    public function test_force_delete_user_also_force_deletes_cart_and_cart_items(): void
    {
        $admin = $this->createTestUser(['email' => 'admin-force-cart@email.com', 'role' => UserRole::ADMIN]);
        $user = $this->createTestUser(['email' => 'user-force-cart@email.com']);
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);

        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $cartItem1 = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
        ]);
        $cartItem2 = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
        ]);

        $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response = $this->actingAs($admin)->deleteJson("api/users/{$user->id}/force-delete");
        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem1->id]);
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem2->id]);
    }

    public function test_soft_delete_user_with_multiple_addresses_and_cart_items(): void
    {
        $user = $this->createTestUser(['email' => 'user-multiple@email.com']);
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);

        $addresses = Address::factory()->count(3)->create(['user_id' => $user->id]);

        $products = Product::factory()->count(5)->create();
        $cartItems = [];
        foreach ($products as $product) {
            $cartItems[] = CartItem::factory()->create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
            ]);
        }

        $response = $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(200);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertSoftDeleted('carts', ['id' => $cart->id]);

        foreach ($addresses as $address) {
            $this->assertSoftDeleted('addresses', ['id' => $address->id]);
        }

        foreach ($cartItems as $cartItem) {
            $this->assertSoftDeleted('cart_items', ['id' => $cartItem->id]);
        }
    }

    public function test_restore_user_with_multiple_addresses_and_cart_items(): void
    {
        $admin = $this->createTestUser(['email' => 'admin-multiple@email.com', 'role' => UserRole::ADMIN]);
        $user = $this->createTestUser(['email' => 'user-restore-multiple@email.com']);
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);

        $addresses = Address::factory()->count(3)->create(['user_id' => $user->id]);

        $products = Product::factory()->count(4)->create();
        $cartItems = [];
        foreach ($products as $product) {
            $cartItems[] = CartItem::factory()->create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
            ]);
        }

        $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response = $this->actingAs($admin)->postJson("api/users/{$user->id}/restore");
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('carts', ['id' => $cart->id, 'deleted_at' => null]);

        foreach ($addresses as $address) {
            $this->assertDatabaseHas('addresses', ['id' => $address->id, 'deleted_at' => null]);
        }

        foreach ($cartItems as $cartItem) {
            $this->assertDatabaseHas('cart_items', ['id' => $cartItem->id, 'deleted_at' => null]);
        }
    }

    public function test_soft_delete_user_without_cart_should_not_fail(): void
    {
        $user = $this->createTestUser(['email' => 'user-no-cart@email.com']);
        $address = Address::factory()->create(['user_id' => $user->id]);

        $user->cart?->forceDelete();

        $response = $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(200);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertSoftDeleted('addresses', ['id' => $address->id]);
    }

    public function test_soft_delete_user_without_addresses_should_not_fail(): void
    {
        $user = $this->createTestUser(['email' => 'user-no-addresses@email.com']);
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);

        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(200);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertSoftDeleted('carts', ['id' => $cart->id]);
        $this->assertSoftDeleted('cart_items', ['id' => $cartItem->id]);
    }

    public function test_soft_delete_user_with_empty_cart_should_still_delete_cart(): void
    {
        $user = $this->createTestUser(['email' => 'user-empty-cart@email.com']);
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(200);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertSoftDeleted('carts', ['id' => $cart->id]);
        $this->assertSoftDeleted('addresses', ['id' => $address->id]);
    }
}
