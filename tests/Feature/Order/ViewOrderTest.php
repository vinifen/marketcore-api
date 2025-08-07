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
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;
    private Order $order;
    private Address $address;
    private Coupon $coupon;

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
        $this->coupon = Coupon::factory()->create();

        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'coupon_id' => $this->coupon->id,
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

    public function test_user_can_view_own_order(): void
    {
        $response = $this->actingAs($this->user)->getJson("api/order/{$this->order->id}");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'user_id' => $this->user->id,
                    'address_id' => $this->address->id,
                    'coupon_id' => $this->coupon->id,
                    'status' => OrderStatus::PENDING->value,
                    'total_amount' => '200.00',
                ]));

        $responseData = $response->json('data');
        $this->assertArrayHasKey('user', $responseData);
        $this->assertArrayHasKey('address', $responseData);
        $this->assertArrayHasKey('coupon', $responseData);
    }

    public function test_user_cannot_view_other_user_order(): void
    {
        $otherUser = $this->createTestUser(['email' => 'other@example.com']);

        $response = $this->actingAs($otherUser)->getJson("api/order/{$this->order->id}");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to view this resource.'));
    }

    public function test_admin_can_view_any_order(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->getJson("api/order/{$this->order->id}");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'user_id' => $this->user->id,
                    'status' => OrderStatus::PENDING->value,
                ]));
    }

    public function test_moderator_can_view_any_order(): void
    {
        $moderator = $this->createTestUser([
            'email' => 'moderator@example.com',
            'role' => UserRole::MODERATOR,
        ]);

        $response = $this->actingAs($moderator)->getJson("api/order/{$this->order->id}");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'user_id' => $this->user->id,
                    'status' => OrderStatus::PENDING->value,
                ]));
    }

    public function test_should_fail_when_order_not_found(): void
    {
        $response = $this->actingAs($this->user)->getJson("api/order/999");

        $response->assertStatus(404)
                ->assertJson($this->defaultErrorResponse('No query results for model [App\\Models\\Order] 999'));
    }

    public function test_should_fail_when_not_authenticated(): void
    {
        $response = $this->getJson("api/order/{$this->order->id}");

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));
    }

    public function test_user_can_view_canceled_order(): void
    {
        $this->order->update(['status' => OrderStatus::CANCELED]);

        $response = $this->actingAs($this->user)->getJson("api/order/{$this->order->id}");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => OrderStatus::CANCELED->value,
                ]));
    }

    public function test_user_can_view_completed_order(): void
    {
        $this->order->update(['status' => OrderStatus::COMPLETED]);

        $response = $this->actingAs($this->user)->getJson("api/order/{$this->order->id}");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => OrderStatus::COMPLETED->value,
                ]));
    }

    public function test_user_can_view_shipped_order(): void
    {
        $this->order->update(['status' => OrderStatus::SHIPPED]);

        $response = $this->actingAs($this->user)->getJson("api/order/{$this->order->id}");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $this->order->id,
                    'status' => OrderStatus::SHIPPED->value,
                ]));
    }

    public function test_regular_user_cannot_view_order_from_different_user(): void
    {
        $regularUser = $this->createTestUser([
            'email' => 'regular@example.com',
            'role' => UserRole::CLIENT,
        ]);

        $response = $this->actingAs($regularUser)->getJson("api/order/{$this->order->id}");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to view this resource.'));
    }

    public function test_soft_deleted_order_cannot_be_viewed_by_regular_user(): void
    {
        $this->order->delete();

        $response = $this->actingAs($this->user)->getJson("api/order/{$this->order->id}");

        $response->assertStatus(404)
                ->assertJson($this->defaultErrorResponse('No query results for model [App\\Models\\Order] ' . $this->order->id));
    }

    public function test_admin_can_view_soft_deleted_order(): void
    {
        $this->order->delete();

        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->getJson("api/order/{$this->order->id}");

        $response->assertStatus(404)
                ->assertJson($this->defaultErrorResponse('No query results for model [App\\Models\\Order] ' . $this->order->id));
    }

    public function test_order_with_items_shows_relationships(): void
    {
        OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => Product::factory()->create(['category_id' => Category::factory()->create()->id])->id,
            'quantity' => 1,
            'unit_price' => 50.00,
        ]);

        $response = $this->actingAs($this->user)->getJson("api/order/{$this->order->id}");

        $response->assertStatus(200);

        $responseData = $response->json('data');
        $this->assertArrayHasKey('user', $responseData);
        $this->assertArrayHasKey('address', $responseData);
        $this->assertArrayHasKey('coupon', $responseData);
        
        $this->assertEquals($this->user->id, $responseData['user']['id']);
        $this->assertEquals($this->user->email, $responseData['user']['email']);
        
        $this->assertEquals($this->address->id, $responseData['address']['id']);
        
        $this->assertEquals($this->coupon->id, $responseData['coupon']['id']);
    }
}
