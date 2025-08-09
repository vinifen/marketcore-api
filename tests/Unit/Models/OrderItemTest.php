<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_item(): void
    {
        $item = OrderItem::factory()->create();
        $this->assertInstanceOf(OrderItem::class, $item);

        $this->assertGreaterThan(0, $item->order_id);
        $this->assertGreaterThan(0, $item->unit_price);
    }

    public function test_item_belongs_to_order_and_product(): void
    {
        $product = Product::factory()->create(['price' => 15.25]);
        $order = Order::factory()->create();
        $item = OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'unit_price' => $product->price,
        ]);

        $orderRelation = $item->order;
        $productRelation = $item->product;
        $this->assertInstanceOf(Order::class, $orderRelation);
        $this->assertInstanceOf(Product::class, $productRelation);
        $this->assertEquals($order->id, $orderRelation->id);
        $this->assertEquals($product->id, $productRelation->id);
    }

    public function test_soft_delete_item_keeps_order(): void
    {
        $order = Order::factory()->create();
        $item = OrderItem::factory()->create(['order_id' => $order->id]);

        $item->delete();
        $this->assertSoftDeleted('order_items', ['id' => $item->id]);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'deleted_at' => null]);
    }
}
