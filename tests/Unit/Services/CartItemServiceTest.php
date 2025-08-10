<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CartItemService;
use App\Services\ProductService;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Cart;
use App\Models\User;
use App\Models\Category;
use App\Exceptions\ApiException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartItemServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var CartItemService */
    private $cartItemService;

    /** @var ProductService */
    private $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartItemService = new CartItemService();
        $this->productService = new ProductService();
    }

    public function test_cart_item_service_can_be_instantiated(): void
    {
        $service = new CartItemService();
        $this->assertInstanceOf(CartItemService::class, $service);
    }

    public function test_store_creates_new_cart_item(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);
        $user = User::factory()->create();

        $cart = $user->cart;
        $this->assertNotNull($cart, 'Cart should be created automatically for user');

        $data = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ];

        $cartItem = $this->cartItemService->store($data, $this->productService);

        $this->assertInstanceOf(CartItem::class, $cartItem);
        $this->assertEquals($cart->id, $cartItem->cart_id);
        $this->assertEquals($product->id, $cartItem->product_id);
        $this->assertEquals(2, $cartItem->quantity);
    }

    public function test_store_updates_existing_cart_item_when_product_already_in_cart(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);
        $user = User::factory()->create();
        $cart = $user->cart;
        $this->assertNotNull($cart, 'Cart should be created automatically for user');

        $existingCartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3
        ]);

        $data = [
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ];

        $cartItem = $this->cartItemService->store($data, $this->productService);

        $this->assertEquals($existingCartItem->id, $cartItem->id);
        $this->assertEquals(5, $cartItem->quantity); // 3 + 2
    }

    public function test_store_throws_exception_when_product_not_found(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart;
        $this->assertNotNull($cart, 'Cart should be created automatically for user');

        $data = [
            'cart_id' => $cart->id,
            'product_id' => 999,
            'quantity' => 1
        ];

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Product not found.');
        $this->expectExceptionCode(404);

        $this->cartItemService->store($data, $this->productService);
    }

    public function test_update_quantity_updates_cart_item_quantity(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'stock' => 10]);
        $user = User::factory()->create();
        $cart = $user->cart;
        $this->assertNotNull($cart, 'Cart should be created automatically for user');

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $updatedCartItem = $this->cartItemService->updateQuantity($cartItem, 5, $this->productService);

        $this->assertEquals(5, $updatedCartItem->quantity);
    }

    public function test_remove_one_decreases_quantity_when_quantity_greater_than_one(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart;
        $this->assertNotNull($cart, 'Cart should be created automatically for user');

        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 3
        ]);

        $this->cartItemService->removeOne($cartItem);

        $cartItem->refresh();
        $this->assertEquals(2, $cartItem->quantity);
    }

    public function test_remove_one_deletes_cart_item_when_quantity_is_one(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart;
        $this->assertNotNull($cart, 'Cart should be created automatically for user');

        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        $cartItemId = $cartItem->id;

        $this->cartItemService->removeOne($cartItem);

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItemId]);
    }
}
