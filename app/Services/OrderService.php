<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ApiException;
use App\Models\Discount;

class OrderService
{
    public function store(): void
    {
        $cart = $this->getCurrentCart();
    }

    protected function calculateTotal(Cart $cart): float
    {
        $total = 0.0;
        foreach ($cart->items as $item) {
            $total += $item->product->price * $item->quantity;
        }
        return $total;
    }

    protected function calculateTotalWithCoupon(Cart $cart, ?float $coupon): float
    {
        $total = $this->calculateTotal($cart);
        if ($coupon) {
            $total -= $total * ($coupon / 100);
        }
        $this->validateTotalAmount($total);
        return $total;
    }

    protected function validateTotalAmount(float $total): void
    {
        if ($total < 0) {
            throw new ApiException('Total amount cannot be negative.', null, 422);
        }
        if ($total < 0.01) {
            throw new ApiException('Total amount must be at least 0.01.', null, 422);
        }
        if ($total > 999999.99) {
            throw new ApiException('Total amount exceeds the maximum limit.', null, 422);
        }
    }

    protected static function getCurrentCart(): Cart
    {
        $cart = Auth::user()?->cart;
        if (!$cart) {
            throw new ApiException('No cart found for the authenticated user.', null, 404);
        }
        return $cart;
    }
}