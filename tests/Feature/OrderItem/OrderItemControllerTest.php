<?php

namespace Tests\Feature\OrderItem;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderItemControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @phpstan-ignore property.uninitialized */
    private User $adminUser;

    /** @phpstan-ignore property.uninitialized */
    private User $staffUser;

    /** @phpstan-ignore property.uninitialized */
    private User $regularUser;

    /** @phpstan-ignore property.uninitialized */
    private Product $product;

    /** @phpstan-ignore property.uninitialized */
    private Order $order;

    /** @phpstan-ignore property.uninitialized */
    private OrderItem $orderItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN
        ]);
        $this->staffUser = $this->createTestUser([
            'email' => 'staff@example.com',
            'role' => UserRole::MODERATOR
        ]);
        $this->regularUser = $this->createTestUser([
            'email' => 'user@example.com',
            'role' => UserRole::CLIENT
        ]);

        $category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100.00,
            'stock' => 10,
        ]);

        $address = Address::factory()->create(['user_id' => $this->regularUser->id]);

        $this->order = Order::factory()->create([
            'user_id' => $this->regularUser->id,
            'address_id' => $address->id,
            'status' => OrderStatus::PENDING,
            'total_amount' => 200.00,
        ]);

        $this->orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 100.00,
        ]);
    }

    public function test_staff_can_view_all_order_items(): void
    {
        $this->actingAs($this->staffUser);

        $response = $this->getJson('/api/order-items');

        $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'order_id',
                            'product_id',
                            'quantity',
                            'unit_price',
                            'order',
                            'product'
                        ]
                    ]
                ]);
    }

    public function test_admin_can_view_all_order_items(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->getJson('/api/order-items');

        $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'order_id',
                            'product_id',
                            'quantity',
                            'unit_price'
                        ]
                    ]
                ]);
    }

    public function test_regular_user_cannot_view_all_order_items(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->getJson('/api/order-items');

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_view_all_order_items(): void
    {
        $response = $this->getJson('/api/order-items');

        $response->assertUnauthorized();
    }

    public function test_order_owner_can_view_their_order_item(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->getJson("/api/order-items/{$this->orderItem->id}");

        $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'order_id',
                        'product_id',
                        'quantity',
                        'unit_price',
                        'order',
                        'product'
                    ]
                ]);
    }

    public function test_staff_can_view_any_order_item(): void
    {
        $this->actingAs($this->staffUser);

        $response = $this->getJson("/api/order-items/{$this->orderItem->id}");

        $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'order_id',
                        'product_id',
                        'quantity',
                        'unit_price'
                    ]
                ]);
    }

    public function test_admin_can_view_any_order_item(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->getJson("/api/order-items/{$this->orderItem->id}");

        $response->assertOk();
    }

    public function test_other_user_cannot_view_order_item(): void
    {
        $otherUser = $this->createTestUser([
            'email' => 'other@example.com',
            'role' => UserRole::CLIENT
        ]);
        $this->actingAs($otherUser);

        $response = $this->getJson("/api/order-items/{$this->orderItem->id}");

        $response->assertForbidden();
    }

    public function test_show_order_item_not_found(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->getJson('/api/order-items/99999');

        $response->assertNotFound();
    }

    public function test_admin_can_create_order_item(): void
    {
        $this->actingAs($this->adminUser);

        $orderItemData = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'unit_price' => 100.00,
        ];

        $response = $this->postJson('/api/order-items', $orderItemData);

        $response->assertCreated()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'order_id',
                        'product_id',
                        'quantity',
                        'unit_price'
                    ]
                ]);

        $this->assertDatabaseHas('order_items', $orderItemData);
    }

    public function test_staff_cannot_create_order_item(): void
    {
        $this->actingAs($this->staffUser);

        $orderItemData = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'unit_price' => 100.00,
        ];

        $response = $this->postJson('/api/order-items', $orderItemData);

        $response->assertForbidden();
    }

    public function test_regular_user_cannot_create_order_item(): void
    {
        $this->actingAs($this->regularUser);

        $orderItemData = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'unit_price' => 100.00,
        ];

        $response = $this->postJson('/api/order-items', $orderItemData);

        $response->assertForbidden();
    }

    public function test_create_order_item_validation_fails(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->postJson('/api/order-items', []);

        $response->assertUnprocessable()
                ->assertJsonValidationErrors(['order_id', 'product_id', 'quantity', 'unit_price']);
    }

    public function test_create_order_item_with_invalid_order_id(): void
    {
        $this->actingAs($this->adminUser);

        $orderItemData = [
            'order_id' => 99999,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'unit_price' => 100.00,
        ];

        $response = $this->postJson('/api/order-items', $orderItemData);

        $response->assertUnprocessable()
                ->assertJsonValidationErrors(['order_id']);
    }

    public function test_create_order_item_with_invalid_product_id(): void
    {
        $this->actingAs($this->adminUser);

        $orderItemData = [
            'order_id' => $this->order->id,
            'product_id' => 99999,
            'quantity' => 3,
            'unit_price' => 100.00,
        ];

        $response = $this->postJson('/api/order-items', $orderItemData);

        $response->assertUnprocessable()
                ->assertJsonValidationErrors(['product_id']);
    }

    public function test_admin_can_update_order_item(): void
    {
        $this->actingAs($this->adminUser);

        $updateData = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'unit_price' => 150.00,
        ];

        $response = $this->putJson("/api/order-items/{$this->orderItem->id}", $updateData);

        $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'order_id',
                        'product_id',
                        'quantity',
                        'unit_price'
                    ]
                ]);

        $this->assertDatabaseHas('order_items', array_merge(['id' => $this->orderItem->id], $updateData));
    }

    public function test_staff_cannot_update_order_item(): void
    {
        $this->actingAs($this->staffUser);

        $updateData = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'unit_price' => 150.00,
        ];

        $response = $this->putJson("/api/order-items/{$this->orderItem->id}", $updateData);

        $response->assertForbidden();
    }

    public function test_regular_user_cannot_update_order_item(): void
    {
        $this->actingAs($this->regularUser);

        $updateData = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'unit_price' => 150.00,
        ];

        $response = $this->putJson("/api/order-items/{$this->orderItem->id}", $updateData);

        $response->assertForbidden();
    }

    public function test_update_order_item_not_found(): void
    {
        $this->actingAs($this->adminUser);

        $updateData = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'unit_price' => 150.00,
        ];

        $response = $this->putJson('/api/order-items/99999', $updateData);

        $response->assertNotFound();
    }

    public function test_admin_can_delete_order_item(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->deleteJson("/api/order-items/{$this->orderItem->id}");

        $response->assertOk();
        $this->assertSoftDeleted('order_items', ['id' => $this->orderItem->id]);
    }

    public function test_staff_cannot_delete_order_item(): void
    {
        $this->actingAs($this->staffUser);

        $response = $this->deleteJson("/api/order-items/{$this->orderItem->id}");

        $response->assertForbidden();
    }

    public function test_regular_user_cannot_delete_order_item(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->deleteJson("/api/order-items/{$this->orderItem->id}");

        $response->assertForbidden();
    }

    public function test_delete_order_item_not_found(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->deleteJson('/api/order-items/99999');

        $response->assertNotFound();
    }

    public function test_admin_can_force_delete_order_item(): void
    {
        $this->actingAs($this->adminUser);

        $this->orderItem->delete();

        $response = $this->deleteJson("/api/order-items/{$this->orderItem->id}/force-delete");

        $response->assertOk();
        $this->assertDatabaseMissing('order_items', ['id' => $this->orderItem->id]);
    }

    public function test_staff_cannot_force_delete_order_item(): void
    {
        $this->actingAs($this->staffUser);

        $this->orderItem->delete();

        $response = $this->deleteJson("/api/order-items/{$this->orderItem->id}/force-delete");

        $response->assertForbidden();
    }

    public function test_regular_user_cannot_force_delete_order_item(): void
    {
        $this->actingAs($this->regularUser);

        $this->orderItem->delete();

        $response = $this->deleteJson("/api/order-items/{$this->orderItem->id}/force-delete");

        $response->assertForbidden();
    }

    public function test_admin_can_restore_order_item(): void
    {
        $this->actingAs($this->adminUser);

        $this->orderItem->delete();
        $this->assertSoftDeleted('order_items', ['id' => $this->orderItem->id]);

        $response = $this->postJson("/api/order-items/{$this->orderItem->id}/restore");

        $response->assertOk()
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'order_id',
                        'product_id',
                        'quantity',
                        'unit_price'
                    ]
                ]);

        $this->assertDatabaseHas('order_items', [
            'id' => $this->orderItem->id,
            'deleted_at' => null
        ]);
    }

    public function test_staff_cannot_restore_order_item(): void
    {
        $this->actingAs($this->staffUser);


        $this->orderItem->delete();

        $response = $this->postJson("/api/order-items/{$this->orderItem->id}/restore");

        $response->assertForbidden();
    }

    public function test_regular_user_cannot_restore_order_item(): void
    {
        $this->actingAs($this->regularUser);

        $this->orderItem->delete();

        $response = $this->postJson("/api/order-items/{$this->orderItem->id}/restore");

        $response->assertForbidden();
    }

    public function test_restore_order_item_not_found(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->postJson('/api/order-items/99999/restore');

        $response->assertNotFound();
    }

    public function test_staff_can_view_soft_deleted_order_items_in_list(): void
    {
        $this->actingAs($this->staffUser);

        $this->orderItem->delete();

        $response = $this->getJson('/api/order-items');

        $response->assertOk();

        $this->assertCount(0, $response->json('data'));
    }

    public function test_order_owner_cannot_view_soft_deleted_order_item(): void
    {
        $this->actingAs($this->regularUser);

        $this->orderItem->delete();

        $response = $this->getJson("/api/order-items/{$this->orderItem->id}");

        $response->assertNotFound();
    }

    public function test_staff_cannot_view_soft_deleted_order_item(): void
    {
        $this->actingAs($this->staffUser);

        $this->orderItem->delete();

        $response = $this->getJson("/api/order-items/{$this->orderItem->id}");

        $response->assertNotFound();
    }
}
