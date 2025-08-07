<?php

namespace Tests\Feature\Order;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;
    private Order $order;
    private Address $address;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createTestUser();
        
        $category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100.00,
            'stock' => 10,
        ]);

        $this->address = Address::factory()->create(['user_id' => $this->user->id]);

        // Create an order
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::PENDING,
            'total_amount' => 200.00,
        ]);

        // Create order items
        OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 100.00,
        ]);
    }

    public function test_admin_can_update_order_status_to_processing(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->putJson("api/order/{$this->order->id}/status", [
            'status' => OrderStatus::PROCESSING->value,
        ]);

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => OrderStatus::PROCESSING->value,
                ]));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'status' => OrderStatus::PROCESSING->value,
        ]);
    }

    public function test_admin_can_update_order_status_to_shipped(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->putJson("api/order/{$this->order->id}/status", [
            'status' => OrderStatus::SHIPPED->value,
        ]);

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => OrderStatus::SHIPPED->value,
                ]));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'status' => OrderStatus::SHIPPED->value,
        ]);
    }

    public function test_admin_can_update_order_status_to_completed(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->putJson("api/order/{$this->order->id}/status", [
            'status' => OrderStatus::COMPLETED->value,
        ]);

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => OrderStatus::COMPLETED->value,
                ]));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'status' => OrderStatus::COMPLETED->value,
        ]);
    }

    public function test_moderator_can_update_order_status(): void
    {
        $moderator = $this->createTestUser([
            'email' => 'moderator@example.com',
            'role' => UserRole::MODERATOR,
        ]);

        $response = $this->actingAs($moderator)->putJson("api/order/{$this->order->id}/status", [
            'status' => OrderStatus::PROCESSING->value,
        ]);

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => OrderStatus::PROCESSING->value,
                ]));
    }

    public function test_regular_user_cannot_update_order_status(): void
    {
        $response = $this->actingAs($this->user)->putJson("api/order/{$this->order->id}/status", [
            'status' => OrderStatus::PROCESSING->value,
        ]);

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to update this resource.'));
    }

    public function test_other_user_cannot_update_order_status(): void
    {
        $otherUser = $this->createTestUser(['email' => 'other@example.com']);

        $response = $this->actingAs($otherUser)->putJson("api/order/{$this->order->id}/status", [
            'status' => OrderStatus::PROCESSING->value,
        ]);

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to update this resource.'));
    }

    public function test_should_fail_with_invalid_status(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->putJson("api/order/{$this->order->id}/status", [
            'status' => 'INVALID_STATUS',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
    }

    public function test_should_fail_when_order_not_found(): void
    {
        $response = $this->actingAs($this->user)->putJson("api/order/999/status", [
            'status' => OrderStatus::PROCESSING->value,
        ]);

        $response->assertStatus(404)
                ->assertJson($this->defaultErrorResponse('No query results for model [App\Models\Order] 999'));
    }

    public function test_should_fail_when_not_authenticated(): void
    {
        $response = $this->putJson("api/order/{$this->order->id}/status", [
            'status' => OrderStatus::PROCESSING->value,
        ]);

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));
    }

    public function test_cannot_update_canceled_order_status(): void
    {
        $this->order->update(['status' => OrderStatus::CANCELED]);

        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->putJson("api/order/{$this->order->id}/status", [
            'status' => OrderStatus::PROCESSING->value,
        ]);

        $response->assertStatus(422)
                ->assertJson($this->defaultErrorResponse('Cannot update status of a canceled order.'));
    }

    public function test_cannot_update_completed_order_status(): void
    {
        $this->order->update(['status' => OrderStatus::COMPLETED]);

        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->putJson("api/order/{$this->order->id}/status", [
            'status' => OrderStatus::PROCESSING->value,
        ]);

        $response->assertStatus(422)
                ->assertJson($this->defaultErrorResponse('Cannot update status of a completed order.'));
    }

    public function test_status_progression_validation(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        // Update to PROCESSING
        $this->order->update(['status' => OrderStatus::PROCESSING]);

        // Try to go back to PENDING (should fail)
        $response = $this->actingAs($admin)->putJson("api/order/{$this->order->id}/status", [
            'status' => OrderStatus::PENDING->value,
        ]);

        $response->assertStatus(422)
                ->assertJson($this->defaultErrorResponse('Invalid status transition from PROCESSING to PENDING.'));
    }
}
