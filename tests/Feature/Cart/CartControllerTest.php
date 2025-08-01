<?php

namespace Tests\Feature\Cart;

use App\Models\User;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_is_created_when_user_is_created(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
        ]);

        $cart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($cart);
        $this->assertEquals($user->id, $cart->user_id);
    }

    public function test_cart_is_deleted_when_user_is_deleted(): void
    {
        $user = User::factory()->create();
        $cartId = $user->cart->id ?? Cart::where('user_id', $user->id)->value('id');

        $user->delete();

        $this->assertDatabaseMissing('carts', [
            'id' => $cartId,
            'user_id' => $user->id,
        ]);
    }
}