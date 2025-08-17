<?php

namespace Tests\Unit\Actions\Orders;

use App\Actions\Orders\CancelOrderAction;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CancelOrderActionTest extends TestCase
{
    use RefreshDatabase;

    /** @phpstan-ignore-next-line */
    private CancelOrderAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CancelOrderAction();
    }

    public function test_it_cancels_order_and_restores_stock(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::PENDING,
        ]);

        $product1 = Product::factory()->create(['stock' => 10]);
        $product2 = Product::factory()->create(['stock' => 5]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 3,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 2,
        ]);

        $productService = new ProductService();
        $result = $this->action->execute($order->load('items.product'), $productService);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals(OrderStatus::CANCELED, $result->status);

        $product1->refresh();
        $product2->refresh();

        $this->assertEquals(13, $product1->stock);
        $this->assertEquals(7, $product2->stock);
    }

    public function test_it_cancels_order_without_products(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::PENDING,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => null,
            'quantity' => 1,
        ]);

        $productService = new ProductService();
        $result = $this->action->execute($order->load('items.product'), $productService);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals(OrderStatus::CANCELED, $result->status);
    }

    public function test_it_cancels_order_with_mixed_items(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::PENDING,
        ]);

        $product = Product::factory()->create(['stock' => 10]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => null,
            'quantity' => 1,
        ]);

        $productService = new ProductService();
        $result = $this->action->execute($order->load('items.product'), $productService);

        $this->assertInstanceOf(Order::class, $result);
        $this->assertEquals(OrderStatus::CANCELED, $result->status);

        $product->refresh();
        $this->assertEquals(12, $product->stock);
    }

    public function test_it_cancels_order_that_is_already_canceled(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::CANCELED,
        ]);

        $product = Product::factory()->create(['stock' => 10]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $productService = new ProductService();
        $result = $this->action->execute($order->load('items.product'), $productService);

        $this->assertEquals(OrderStatus::CANCELED, $result->status);

        $product->refresh();
        $this->assertEquals(12, $product->stock);
    }

    public function test_it_cancels_completed_order_and_restores_stock(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => OrderStatus::COMPLETED,
        ]);

        $product = Product::factory()->create(['stock' => 5]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $productService = new ProductService();
        $result = $this->action->execute($order->load('items.product'), $productService);

        $this->assertEquals(OrderStatus::CANCELED, $result->status);

        $product->refresh();
        $this->assertEquals(8, $product->stock);
    }
}
