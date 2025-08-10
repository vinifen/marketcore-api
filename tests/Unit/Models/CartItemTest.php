<?php

namespace Tests\Unit\Models;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_item_can_be_created_with_factory(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 10.5]);
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertInstanceOf(CartItem::class, $cartItem);
        $this->assertEquals($cart->id, $cartItem->cart_id);
        $this->assertEquals($product->id, $cartItem->product_id);
        $this->assertEquals(2, $cartItem->quantity);
    }

    public function test_cart_item_belongs_to_cart(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $this->assertInstanceOf(Cart::class, $cartItem->cart);
        $this->assertEquals($cart->id, $cartItem->cart->id);
    }

    public function test_cart_item_belongs_to_product(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ]);

        $this->assertInstanceOf(Product::class, $cartItem->product);
        $this->assertEquals($product->id, $cartItem->product->id);
    }

    public function test_fillable_attributes(): void
    {
        $cartItem = new CartItem();

        $this->assertEquals([
            'cart_id',
            'product_id',
            'quantity',
        ], $cartItem->getFillable());
    }

    public function test_casts_attributes(): void
    {
        $cartItem = new CartItem();

        $this->assertEquals([
            'id' => 'integer',
            'cart_id' => 'integer',
            'product_id' => 'integer',
            'quantity' => 'integer',
            'deleted_at' => 'datetime',
        ], $cartItem->getCasts());
    }
}
