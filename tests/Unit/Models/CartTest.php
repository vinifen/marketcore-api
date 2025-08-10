<?php

namespace Tests\Unit\Models;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_belongs_to_user(): void
    {
        $user = $this->createTestUser();
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);
        $this->assertEquals($user->id, $cart->user_id);
    }

    public function test_user_has_only_one_cart(): void
    {
        $user = $this->createTestUser();
        $cart1 = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);
        $cart2 = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($cart1->id, $cart2->id);
        $this->assertEquals(1, Cart::where('user_id', $user->id)->count());
    }

    public function test_cart_user_relationship(): void
    {
        $user = $this->createTestUser();
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $cart->user);
        $this->assertEquals($user->id, $cart->user->id);
    }

    public function test_user_without_cart_returns_null(): void
    {
        $user = $this->createTestUser();
        $cart = $user->cart ?? Cart::factory()->create(['user_id' => $user->id]);
        $cart->delete();

        $user = User::find($user->id);
        $this->assertNotNull($user);
        $this->assertNull($user->cart);
    }
}
