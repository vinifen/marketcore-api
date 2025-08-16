<?php

namespace Tests\Unit\Actions\Orders;

use App\Actions\Orders\StoreOrderAction;
use App\Enums\OrderStatus;
use App\Exceptions\ApiException;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreOrderActionTest extends TestCase
{
    use RefreshDatabase;

    /** @phpstan-ignore-next-line */
    private StoreOrderAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new StoreOrderAction();
    }

    public function test_it_creates_order_successfully(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 10.00, 'stock' => 10]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $data = [
            'address_id' => $address->id,
        ];

        $productService = new ProductService();
        $result = $this->action->execute($data, $user->id, $productService);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($address->id, $result->address_id);
        $this->assertEquals(20.00, $result->total_amount);
        $this->assertEquals(OrderStatus::PENDING, $result->status);

        $this->assertCount(1, $result->items);
        $firstItem = $result->items->first();
        $this->assertNotNull($firstItem);
        $this->assertEquals($product->id, $firstItem->product_id);
        $this->assertEquals(2, $firstItem->quantity);
        $this->assertEquals(10.00, $firstItem->unit_price);

        $product->refresh();
        $this->assertEquals(8, $product->stock);

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
        ]);
    }

    public function test_it_creates_order_with_coupon(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);
        $coupon = Coupon::factory()->create([
            'code' => 'DISCOUNT20',
            'discount_percentage' => 20.0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(7),
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $data = [
            'address_id' => $address->id,
            'coupon_code' => 'DISCOUNT20',
        ];

        $productService = new ProductService();
        $result = $this->action->execute($data, $user->id, $productService);

        $this->assertEquals(80.00, $result->total_amount);
        $this->assertEquals($coupon->id, $result->coupon_id);
        $this->assertNotNull($result->coupon);
    }

    public function test_it_creates_order_with_discounted_product(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 100.00, 'stock' => 10]);

        $product->discounts()->create([
            'discount_percentage' => 25.0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $data = [
            'address_id' => $address->id,
        ];

        $productService = new ProductService();
        $result = $this->action->execute($data, $user->id, $productService);

        $this->assertEquals(75.00, $result->total_amount);
        $firstItem = $result->items->first();
        $this->assertNotNull($firstItem);
        $this->assertEquals(75.00, $firstItem->unit_price);
    }

    public function test_it_throws_exception_when_user_not_found(): void
    {
        $data = ['address_id' => 1];
        $productService = new ProductService();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('User not found.');
        $this->expectExceptionCode(404);

        $this->action->execute($data, 999, $productService);
    }

    public function test_it_throws_exception_when_cart_is_empty(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $address = Address::factory()->create(['user_id' => $user->id]);

        $data = ['address_id' => $address->id];
        $productService = new ProductService();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('User cart is empty.');
        $this->expectExceptionCode(422);

        $this->action->execute($data, $user->id, $productService);
    }

    public function test_it_throws_exception_when_coupon_not_found(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 10.00, 'stock' => 10]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $data = [
            'address_id' => $address->id,
            'coupon_code' => 'INVALID',
        ];

        $productService = new ProductService();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Coupon not found or expired.');
        $this->expectExceptionCode(404);

        $this->action->execute($data, $user->id, $productService);
    }

    public function test_it_throws_exception_when_insufficient_stock(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 10.00, 'stock' => 1]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $data = ['address_id' => $address->id];
        $productService = new ProductService();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Cannot decrease stock by 5.');

        $this->action->execute($data, $user->id, $productService);
    }

    public function test_it_throws_exception_when_total_is_negative(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 10.00, 'stock' => 10]);
        $coupon = Coupon::factory()->create([
            'code' => 'DISCOUNT150',
            'discount_percentage' => 150.0,
            'start_date' => now()->subDays(1),
            'end_date' => now()->addDays(7),
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $data = [
            'address_id' => $address->id,
            'coupon_code' => 'DISCOUNT150',
        ];

        $productService = new ProductService();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Total amount cannot be negative.');
        $this->expectExceptionCode(422);

        $this->action->execute($data, $user->id, $productService);
    }

    public function test_it_creates_order_with_multiple_products(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $address = Address::factory()->create(['user_id' => $user->id]);

        $product1 = Product::factory()->create(['price' => 15.50, 'stock' => 10]);
        $product2 = Product::factory()->create(['price' => 25.75, 'stock' => 5]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product1->id,
            'quantity' => 2,
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product2->id,
            'quantity' => 1,
        ]);

        $data = ['address_id' => $address->id];
        $productService = new ProductService();

        $result = $this->action->execute($data, $user->id, $productService);

        $this->assertEquals(56.75, $result->total_amount);
        $this->assertCount(2, $result->items);

        $product1->refresh();
        $product2->refresh();

        $this->assertEquals(8, $product1->stock);
        $this->assertEquals(4, $product2->stock);
    }

    public function test_it_validates_maximum_total_amount(): void
    {
        $user = User::factory()->create();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $address = Address::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 999999.99, 'stock' => 10]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $data = ['address_id' => $address->id];
        $productService = new ProductService();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Total amount exceeds the maximum limit.');
        $this->expectExceptionCode(422);

        $this->action->execute($data, $user->id, $productService);
    }
}
