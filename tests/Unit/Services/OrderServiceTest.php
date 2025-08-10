<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Models\Order;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\Category;
use App\Enums\OrderStatus;
use App\Exceptions\ApiException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var OrderService */
    private $orderService;

    /** @var ProductService */
    private $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderService = new OrderService();
        $this->productService = new ProductService();
    }

    public function test_order_service_can_be_instantiated(): void
    {
        $service = new OrderService();
        $this->assertInstanceOf(OrderService::class, $service);
    }

    public function test_store_creates_order_with_cart_items(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart;
        $this->assertNotNull($cart, 'Cart should be created automatically for user');

        $address = Address::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'price' => 10.00, 'stock' => 10]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $data = [
            'address_id' => $address->id,
            'order_date' => now()
        ];

        $order = $this->orderService->store($data, $user->id, $this->productService);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(20.00, $order->total_amount);
        $this->assertEquals(OrderStatus::PENDING, $order->status);
        $this->assertCount(1, $order->items);
    }

    public function test_store_applies_coupon_discount_when_valid_coupon_provided(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart;
        $this->assertNotNull($cart, 'Cart should be created automatically for user');

        $address = Address::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'price' => 100.00, 'stock' => 10]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $coupon = Coupon::factory()->create([
            'code' => 'DISCOUNT20',
            'discount_percentage' => 20.0,
            'start_date' => now()->subDays(2)->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d')
        ]);

        $data = [
            'address_id' => $address->id,
            'coupon_code' => 'DISCOUNT20'
        ];

        $order = $this->orderService->store($data, $user->id, $this->productService);

        $this->assertEquals(80.00, $order->total_amount); // 100 - 20% = 80
        $this->assertEquals($coupon->id, $order->coupon_id);
    }

    public function test_store_throws_exception_for_invalid_coupon(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart;
        $this->assertNotNull($cart, 'Cart should be created automatically for user');

        $address = Address::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'price' => 50.00, 'stock' => 5]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $data = [
            'address_id' => $address->id,
            'coupon_code' => 'INVALID_COUPON'
        ];

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Coupon not found or expired.');
        $this->expectExceptionCode(404);

        $this->orderService->store($data, $user->id, $this->productService);
    }

    public function test_store_decreases_product_stock(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart;
        $this->assertNotNull($cart, 'Cart should be created automatically for user');

        $address = Address::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'price' => 15.00, 'stock' => 10]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $data = [
            'address_id' => $address->id
        ];

        $this->orderService->store($data, $user->id, $this->productService);

        $product->refresh();
        $this->assertEquals(8, $product->stock); // 10 - 2 = 8
    }

    public function test_cancel_order_sets_status_to_cancelled(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'status' => OrderStatus::PENDING
        ]);

        $cancelledOrder = $this->orderService->cancelOrder($order, $this->productService);

        $this->assertEquals(OrderStatus::CANCELED, $cancelledOrder->status);
    }

    public function test_cancel_order_works_even_when_already_cancelled(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'address_id' => $address->id,
            'status' => OrderStatus::CANCELED
        ]);

        $cancelledOrder = $this->orderService->cancelOrder($order, $this->productService);

        $this->assertEquals(OrderStatus::CANCELED, $cancelledOrder->status);
    }

    public function test_stock_throws_exception_when_insufficient_stock(): void
    {
        $user = User::factory()->create();
        $cart = $user->cart;
        $this->assertNotNull($cart, 'Cart should be created automatically for user');

        $address = Address::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 25.00,
            'stock' => 1,
            'name' => 'Test Product'
        ]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        $data = [
            'address_id' => $address->id
        ];

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage("Cannot decrease stock by 5. Only 1 units available for product 'Test Product'.");

        $this->orderService->store($data, $user->id, $this->productService);
    }
}
