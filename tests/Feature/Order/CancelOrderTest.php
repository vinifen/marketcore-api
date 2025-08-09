<?php

namespace Tests\Feature\Order;

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

class CancelOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @phpstan-ignore property.uninitialized */
    private User $user;

    /** @phpstan-ignore property.uninitialized */
    private Product $product;

    /** @phpstan-ignore property.uninitialized */
    private Order $order;

    /** @phpstan-ignore property.uninitialized */
    private Address $address;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createTestUser();

        $category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100.00,
            'stock' => 8,
        ]);

        $this->address = Address::factory()->create(['user_id' => $this->user->id]);

        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::PENDING,
            'total_amount' => 200.00,
        ]);

        OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 100.00,
        ]);
    }

    public function test_should_cancel_order_successfully(): void
    {
        $response = $this->actingAs($this->user)->postJson("api/order/{$this->order->id}/cancel");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => OrderStatus::CANCELED->value,
                ]));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'status' => OrderStatus::CANCELED->value,
        ]);

        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock);
    }

    public function test_should_fail_to_cancel_already_canceled_order(): void
    {
        $this->order->update(['status' => OrderStatus::CANCELED]);

        $response = $this->actingAs($this->user)->postJson("api/order/{$this->order->id}/cancel");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to cancel this resource.'));
    }

    public function test_should_fail_to_cancel_completed_order(): void
    {
        $this->order->update(['status' => OrderStatus::COMPLETED]);

        $response = $this->actingAs($this->user)->postJson("api/order/{$this->order->id}/cancel");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to cancel this resource.'));
    }

    public function test_should_fail_to_cancel_shipped_order(): void
    {
        $this->order->update(['status' => OrderStatus::SHIPPED]);

        $response = $this->actingAs($this->user)->postJson("api/order/{$this->order->id}/cancel");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to cancel this resource.'));
    }

    public function test_user_can_only_cancel_own_orders(): void
    {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)->postJson("api/order/{$this->order->id}/cancel");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to cancel this resource.'));
    }

    public function test_admin_can_cancel_any_order(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->postJson("api/order/{$this->order->id}/cancel");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => OrderStatus::CANCELED->value,
                ]));

        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock);
    }

    public function test_admin_can_cancel_completed_order(): void
    {
        $this->order->update(['status' => OrderStatus::COMPLETED]);

        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->postJson("api/order/{$this->order->id}/cancel");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => OrderStatus::CANCELED->value,
                ]));
    }

    public function test_admin_can_cancel_shipped_order(): void
    {
        $this->order->update(['status' => OrderStatus::SHIPPED]);

        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->postJson("api/order/{$this->order->id}/cancel");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => OrderStatus::CANCELED->value,
                ]));
    }

    public function test_admin_can_cancel_already_canceled_order(): void
    {
        $this->order->update(['status' => OrderStatus::CANCELED]);

        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->postJson("api/order/{$this->order->id}/cancel");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => OrderStatus::CANCELED->value,
                ]));
    }

    public function test_moderator_cannot_cancel_other_user_order(): void
    {
        $moderator = $this->createTestUser([
            'email' => 'moderator@example.com',
            'role' => UserRole::MODERATOR,
        ]);

        $response = $this->actingAs($moderator)->postJson("api/order/{$this->order->id}/cancel");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to cancel this resource.'));
    }

    public function test_should_fail_when_not_authenticated(): void
    {
        $response = $this->postJson("api/order/{$this->order->id}/cancel");

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));
    }

    public function test_should_fail_when_order_not_found(): void
    {
        $response = $this->actingAs($this->user)->postJson("api/order/999/cancel");

        $response->assertStatus(404)
                ->assertJson($this->defaultErrorResponse('Not found.'));
    }

    public function test_should_restore_stock_for_multiple_products(): void
    {
        $category = Category::factory()->create();
        $product2 = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 50.00,
            'stock' => 5,
        ]);

        OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $product2->id,
            'quantity' => 3,
            'unit_price' => 50.00,
        ]);

        $response = $this->actingAs($this->user)->postJson("api/order/{$this->order->id}/cancel");

        $response->assertStatus(200);

        $this->product->refresh();
        $product2->refresh();

        $this->assertEquals(10, $this->product->stock); // 8 + 2 = 10
        $this->assertEquals(8, $product2->stock); // 5 + 3 = 8
    }

    public function test_moderator_can_cancel_own_order(): void
    {
        $moderator = $this->createTestUser([
            'email' => 'moderator@example.com',
            'role' => UserRole::MODERATOR,
        ]);

        // Create order for the moderator
        $moderatorOrder = Order::factory()->create([
            'user_id' => $moderator->id,
            'address_id' => Address::factory()->create(['user_id' => $moderator->id])->id,
            'status' => OrderStatus::PENDING,
            'total_amount' => 200.00,
        ]);

        $response = $this->actingAs($moderator)->postJson("api/order/{$moderatorOrder->id}/cancel");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $moderatorOrder->id,
                    'status' => OrderStatus::CANCELED->value,
                ]));
    }

    public function test_moderator_cannot_cancel_own_completed_order(): void
    {
        $moderator = $this->createTestUser([
            'email' => 'moderator@example.com',
            'role' => UserRole::MODERATOR,
        ]);

        $moderatorOrder = Order::factory()->create([
            'user_id' => $moderator->id,
            'address_id' => Address::factory()->create(['user_id' => $moderator->id])->id,
            'status' => OrderStatus::COMPLETED,
            'total_amount' => 200.00,
        ]);

        $response = $this->actingAs($moderator)->postJson("api/order/{$moderatorOrder->id}/cancel");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to cancel this resource.'));
    }
}
