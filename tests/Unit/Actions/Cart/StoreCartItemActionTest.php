<?php

namespace Tests\Unit\Actions\Cart;

use App\Actions\Cart\StoreCartItemAction;
use App\Exceptions\ApiException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreCartItemActionTest extends TestCase
{
    use RefreshDatabase;

    /** @phpstan-ignore-next-line */
    private StoreCartItemAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new StoreCartItemAction(new ProductService());
    }

    public function test_it_creates_new_cart_item_when_product_not_in_cart(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);

        $data = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ];

        $result = $this->action->execute($data);

        $this->assertInstanceOf(CartItem::class, $result);
        $this->assertEquals($cart->id, $result->cart_id);
        $this->assertEquals($product->id, $result->product_id);
        $this->assertEquals(2, $result->quantity);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_it_uses_default_quantity_of_one_when_not_provided(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);

        $data = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
        ];

        $result = $this->action->execute($data);

        $this->assertEquals(1, $result->quantity);
    }

    public function test_it_updates_existing_cart_item_when_product_already_in_cart(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 10]);
        $existingItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $data = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ];

        $result = $this->action->execute($data);

        $this->assertInstanceOf(CartItem::class, $result);
        $this->assertEquals($existingItem->id, $result->id);
        $this->assertEquals(5, $result->quantity);
        $this->assertDatabaseHas('cart_items', [
            'id' => $existingItem->id,
            'quantity' => 5,
        ]);
    }

    public function test_it_throws_exception_when_product_not_found(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $data = [
            'cart_id' => $cart->id,
            'product_id' => 999,
            'quantity' => 1,
        ];

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Product not found.');
        $this->expectExceptionCode(404);

        $this->action->execute($data);
    }

    public function test_it_throws_exception_when_insufficient_stock_for_new_item(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 5]);

        $data = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 10,
        ];

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Insufficient stock.');

        $this->action->execute($data);
    }

    public function test_it_throws_exception_when_insufficient_stock_for_updated_item(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $product = Product::factory()->create(['stock' => 5]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $data = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ];

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Insufficient stock.');

        $this->action->execute($data);
    }
}
