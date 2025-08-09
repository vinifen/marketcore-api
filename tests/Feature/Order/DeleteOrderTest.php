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

class DeleteOrderTest extends TestCase
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
            'stock' => 10,
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

    public function test_admin_can_soft_delete_order(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->deleteJson("api/order/{$this->order->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('orders', [
            'id' => $this->order->id,
        ]);
    }

    public function test_admin_can_restore_soft_deleted_order(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $this->order->delete();

        $response = $this->actingAs($admin)->postJson("api/order/{$this->order->id}/restore");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => $this->order->status->value,
                ]));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_force_delete_order(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->deleteJson("api/order/{$this->order->id}/force-delete");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('orders', [
            'id' => $this->order->id,
        ]);
    }

    public function test_moderator_cannot_soft_delete_order(): void
    {
        $moderator = $this->createTestUser([
            'email' => 'moderator@example.com',
            'role' => UserRole::MODERATOR,
        ]);

        $response = $this->actingAs($moderator)->deleteJson("api/order/{$this->order->id}");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'deleted_at' => null,
        ]);
    }

    public function test_moderator_cannot_restore_order(): void
    {
        $moderator = $this->createTestUser([
            'email' => 'moderator@example.com',
            'role' => UserRole::MODERATOR,
        ]);

        $this->order->delete();

        $response = $this->actingAs($moderator)->postJson("api/order/{$this->order->id}/restore");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to restore this resource.'));

        $this->assertSoftDeleted('orders', [
            'id' => $this->order->id,
        ]);
    }

    public function test_moderator_cannot_force_delete_order(): void
    {
        $moderator = $this->createTestUser([
            'email' => 'moderator@example.com',
            'role' => UserRole::MODERATOR,
        ]);

        $response = $this->actingAs($moderator)->deleteJson("api/order/{$this->order->id}/force-delete");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'deleted_at' => null,
        ]);
    }

    public function test_regular_user_cannot_soft_delete_order(): void
    {
        $response = $this->actingAs($this->user)->deleteJson("api/order/{$this->order->id}");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'deleted_at' => null,
        ]);
    }

    public function test_regular_user_cannot_restore_order(): void
    {
        $this->order->delete();

        $response = $this->actingAs($this->user)->postJson("api/order/{$this->order->id}/restore");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to restore this resource.'));

        $this->assertSoftDeleted('orders', [
            'id' => $this->order->id,
        ]);
    }

    public function test_regular_user_cannot_force_delete_order(): void
    {
        $response = $this->actingAs($this->user)->deleteJson("api/order/{$this->order->id}/force-delete");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'deleted_at' => null,
        ]);
    }

    public function test_other_user_cannot_soft_delete_order(): void
    {
        $otherUser = $this->createTestUser(['email' => 'other@example.com']);

        $response = $this->actingAs($otherUser)->deleteJson("api/order/{$this->order->id}");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'deleted_at' => null,
        ]);
    }

    public function test_other_user_cannot_restore_order(): void
    {
        $otherUser = $this->createTestUser(['email' => 'other@example.com']);

        $this->order->delete();

        $response = $this->actingAs($otherUser)->postJson("api/order/{$this->order->id}/restore");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to restore this resource.'));

        $this->assertSoftDeleted('orders', [
            'id' => $this->order->id,
        ]);
    }

    public function test_other_user_cannot_force_delete_order(): void
    {
        $otherUser = $this->createTestUser(['email' => 'other@example.com']);

        $response = $this->actingAs($otherUser)->deleteJson("api/order/{$this->order->id}/force-delete");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'deleted_at' => null,
        ]);
    }

    public function test_unauthenticated_user_cannot_soft_delete_order(): void
    {
        $response = $this->deleteJson("api/order/{$this->order->id}");

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'deleted_at' => null,
        ]);
    }

    public function test_unauthenticated_user_cannot_restore_order(): void
    {
        $this->order->delete();

        $response = $this->postJson("api/order/{$this->order->id}/restore");

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));

        $this->assertSoftDeleted('orders', [
            'id' => $this->order->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_force_delete_order(): void
    {
        $response = $this->deleteJson("api/order/{$this->order->id}/force-delete");

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'deleted_at' => null,
        ]);
    }

    public function test_should_fail_when_order_not_found_for_delete(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->deleteJson("api/order/999");

        $response->assertStatus(404)
                ->assertJson($this->defaultErrorResponse('Not found.'));
    }

    public function test_should_fail_when_order_not_found_for_restore(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->postJson("api/order/999/restore");

        $response->assertStatus(404)
                ->assertJson($this->defaultErrorResponse('Trashed model not found.'));
    }

    public function test_should_fail_when_order_not_found_for_force_delete(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->deleteJson("api/order/999/force-delete");

        $response->assertStatus(404)
                ->assertJson($this->defaultErrorResponse('Not found.'));
    }

    public function test_admin_can_delete_order_in_any_status(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $this->order->update(['status' => OrderStatus::COMPLETED]);

        $response = $this->actingAs($admin)->deleteJson("api/order/{$this->order->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('orders', [
            'id' => $this->order->id,
        ]);
    }

    public function test_admin_can_force_delete_order_in_any_status(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $this->order->update(['status' => OrderStatus::CANCELED]);

        $response = $this->actingAs($admin)->deleteJson("api/order/{$this->order->id}/force-delete");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('orders', [
            'id' => $this->order->id,
        ]);
    }

    public function test_cascade_soft_delete_order_items(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);
        OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 50.00,
        ]);

        $response = $this->actingAs($admin)->deleteJson("api/order/{$this->order->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('orders', [
            'id' => $this->order->id,
        ]);

        $this->assertSoftDeleted('order_items', [
            'order_id' => $this->order->id,
        ]);
    }
}
