<?php

namespace Tests\Feature\Order;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @phpstan-ignore property.uninitialized */
    private User $user;

    /** @phpstan-ignore property.uninitialized */
    private Product $product;

    /** @phpstan-ignore property.uninitialized */
    private Cart $cart;

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

        $this->cart = $this->user->cart ?: Cart::factory()->create(['user_id' => $this->user->id]);
        CartItem::factory()->create([
            'cart_id' => $this->cart->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $this->address = Address::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_should_create_order_successfully(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/order', [
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::PENDING->value,
        ]);

        $response->assertStatus(201)
                ->assertJson($this->defaultSuccessResponse([
                    'user_id' => $this->user->id,
                    'total_amount' => 200.00,
                    'status' => OrderStatus::PENDING->value,
                ]));

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total_amount' => 200.00,
            'status' => OrderStatus::PENDING,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 100.00,
        ]);

        $this->product->refresh();
        $this->assertEquals(8, $this->product->stock);

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $this->cart->id,
        ]);
    }

    public function test_should_create_order_with_coupon(): void
    {
        $coupon = Coupon::factory()->create([
            'code' => 'DISCOUNT10',
            'discount_percentage' => 10.0,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);

        $response = $this->actingAs($this->user)->postJson('api/order', [
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::PENDING->value,
            'coupon_code' => 'DISCOUNT10',
        ]);

        $response->assertStatus(201)
                ->assertJson($this->defaultSuccessResponse([
                    'user_id' => $this->user->id,
                    'total_amount' => 180.00, // 200 - 10%
                    'status' => OrderStatus::PENDING->value,
                    'coupon_id' => $coupon->id,
                ]));

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total_amount' => 180.00,
            'coupon_id' => $coupon->id,
        ]);
    }

    public function test_should_fail_when_user_not_found(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/order', [
            'user_id' => 999,
            'address_id' => $this->address->id,
            'status' => OrderStatus::PENDING->value,
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['user_id']);
    }

    public function test_should_fail_when_address_not_found(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/order', [
            'user_id' => $this->user->id,
            'address_id' => 999,
            'status' => OrderStatus::PENDING->value,
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['address_id']);
    }

    public function test_should_fail_when_cart_is_empty(): void
    {
        $userWithoutCart = $this->createTestUser(['email' => 'nocart@example.com']);
        $address = Address::factory()->create(['user_id' => $userWithoutCart->id]);

        $response = $this->actingAs($userWithoutCart)->postJson('api/order', [
            'user_id' => $userWithoutCart->id,
            'address_id' => $address->id,
            'status' => OrderStatus::PENDING->value,
        ]);

        $response->assertStatus(404)
                ->assertJson($this->defaultErrorResponse('No cart found for the authenticated user.'));
    }

    public function test_should_fail_with_invalid_coupon(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/order', [
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::PENDING->value,
            'coupon_code' => 'INVALID_COUPON',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['coupon_code']);
    }

    public function test_should_fail_with_insufficient_stock(): void
    {
        $this->product->update(['stock' => 1]);

        $response = $this->actingAs($this->user)->postJson('api/order', [
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::PENDING->value,
        ]);

        $response->assertStatus(422)
                ->assertJson($this->defaultErrorResponse(
                    'Cannot decrease stock by 2. Only 1 units available for product \'' .
                    $this->product->name .
                    '\'.'
                ));
    }

    public function test_should_fail_when_not_authenticated(): void
    {
        $response = $this->postJson('api/order', [
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::PENDING->value,
        ]);

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));
    }

    public function test_should_fail_with_invalid_status(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/order', [
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'status' => 'INVALID_STATUS',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']);
    }

    public function test_should_fail_when_missing_required_fields(): void
    {
        $response = $this->actingAs($this->user)->postJson('api/order', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['user_id', 'address_id', 'status']);
    }

    public function test_admin_should_be_able_to_create_order_for_any_user(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->postJson('api/order', [
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::PENDING->value,
        ]);

        $response->assertStatus(201)
                ->assertJson($this->defaultSuccessResponse([
                    'user_id' => $this->user->id,
                    'total_amount' => 200.00,
                    'status' => OrderStatus::PENDING->value,
                ]));
    }

    public function test_moderator_should_not_be_able_to_create_order_for_other_user(): void
    {
        $moderator = $this->createTestUser([
            'email' => 'moderator@example.com',
            'role' => UserRole::MODERATOR,
        ]);

        $response = $this->actingAs($moderator)->postJson('api/order', [
            'user_id' => $this->user->id,
            'address_id' => $this->address->id,
            'status' => OrderStatus::PENDING->value,
        ]);

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to create this resource.'));
    }
}
