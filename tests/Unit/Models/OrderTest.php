<?php

namespace Tests\Unit\Models;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_order_with_pending_status(): void
    {
        $order = Order::factory()->create();
        $this->assertInstanceOf(Order::class, $order);
        $this->assertInstanceOf(OrderStatus::class, $order->status);
        $this->assertEquals(OrderStatus::PENDING, $order->status);
    }

    public function test_order_items_relation_returns_created_items(): void
    {
        $order = Order::factory()->create();
        $items = OrderItem::factory()->count(3)->create([
            'order_id' => $order->id,
        ]);

        $this->assertCount(3, $items);
        $this->assertCount(3, $order->items()->get());
        $this->assertEqualsCanonicalizing(
            $items->pluck('id')->toArray(),
            $order->items->pluck('id')->toArray()
        );
    }

    public function test_soft_delete_order_cascades_to_items(): void
    {
        $order = Order::factory()->create();
        $item = OrderItem::factory()->create(['order_id' => $order->id]);
        $this->assertNull($order->deleted_at);
        $this->assertNull($item->deleted_at);

        $order->delete();
        $order->refresh();
        $item->refresh();

        $this->assertNotNull($order->deleted_at);
        $this->assertNotNull($item->deleted_at, 'Order item should be soft deleted with order');
    }

    public function test_restoring_order_restores_items(): void
    {
        $order = Order::factory()->create();
        $item1 = OrderItem::factory()->create(['order_id' => $order->id]);
        $item2 = OrderItem::factory()->create(['order_id' => $order->id]);

        $order->delete();
        $this->assertSoftDeleted('orders', ['id' => $order->id]);
        $this->assertSoftDeleted('order_items', ['id' => $item1->id]);
        $this->assertSoftDeleted('order_items', ['id' => $item2->id]);

        $order->restore();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('order_items', ['id' => $item1->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('order_items', ['id' => $item2->id, 'deleted_at' => null]);
    }

    public function test_order_status_casting_allows_updating_enum(): void
    {
        $order = Order::factory()->create();
        $order->update(['status' => OrderStatus::COMPLETED]);
        $order->refresh();
        $this->assertEquals(OrderStatus::COMPLETED, $order->status);
    }

    public function test_deleting_item_alone_does_not_delete_order(): void
    {
        $order = Order::factory()->create();
        $item = OrderItem::factory()->create(['order_id' => $order->id]);

        $item->delete();
        $this->assertSoftDeleted('order_items', ['id' => $item->id]);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'deleted_at' => null]);
    }

    public function test_adding_items_total_amount_manual_recalculation_example(): void
    {
        $order = Order::factory()->create(['total_amount' => 0]);
        $p1 = Product::factory()->create(['price' => 10.50]);
        $p2 = Product::factory()->create(['price' => 20.00]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $p1->id,
            'quantity' => 2,
            'unit_price' => $p1->price
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $p2->id,
            'quantity' => 1,
            'unit_price' => $p2->price
        ]);

        $expected = (2 * 10.50) + (1 * 20.00); // 41.00
        $order->update(['total_amount' => $expected]);
        $order->refresh();
        $this->assertEquals(41.00, $order->total_amount);
    }
}
