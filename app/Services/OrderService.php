<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ApiException;
use App\Models\Coupon;
use App\Models\Discount;

class OrderService
{
    public function store(array $data): void
    {
        $cart = $this->getCurrentCart();

        $coupon = null;
        if (!empty($data['coupon_code'])) {
            $coupon = Coupon::findCouponByCode($data['coupon_code']);
            if (!$coupon) {
                throw new ApiException('Coupon not found or expired.', null, 404);
            }
            $data['coupon_id'] = $coupon->id;
        } else {
            $data['coupon_id'] = null;
        }

        $couponPercentage = $coupon->discount_percentage ?? null;
        $total = $this->calculateTotalWithCoupon($cart, $couponPercentage);

        
    }

    protected function calculateTotal(Cart $cart): float
    {
        $total = 0.0;
        foreach ($cart->items as $item) {
            $total += $item->unit_price * $item->quantity;
        }
        return $total;
    }

    protected function calculateTotalWithCoupon(Cart $cart, ?float $couponPercentage): float
    {
        $total = $this->calculateTotal($cart);

        if ($couponPercentage !== null && $couponPercentage > 0) {
            $total -= $total * ($couponPercentage / 100);
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

    public function cancelOrder(Order $order): void
    {
        $order->status = OrderStatus::CANCELED;
        $order->save();
    }
}