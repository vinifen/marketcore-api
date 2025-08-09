<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\ApiException;
use App\Models\Coupon;

class OrderService
{
    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data, int $user_id, ProductService $productService): Order
    {
        $cart = $this->getCartForUser($user_id);

        if ($cart->items->isEmpty()) {
            throw new ApiException('User cart is empty.', null, 422);
        }

        $coupon = $this->getCouponFromData($data);
        $data['coupon_id'] = $coupon?->id;

        $totalPrice = $this->calculateTotalWithCoupon($cart, $coupon?->discount_percentage);

        $this->decreaseProductsStock($cart, $productService);

        $order = $this->createOrder($user_id, $totalPrice, $data);

        $this->createOrderItems($order, $cart);

        $cart->items()->forceDelete();

        return $order->load(['items', 'user', 'coupon']);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getCouponFromData(array $data): ?Coupon
    {
        if (!empty($data['coupon_code'])) {
            $coupon = Coupon::findCouponByCode($data['coupon_code']);
            if (!$coupon) {
                throw new ApiException('Coupon not found or expired.', null, 404);
            }
            return $coupon;
        }
        return null;
    }

    private function decreaseProductsStock(Cart $cart, ProductService $productService): void
    {
        foreach ($cart->items as $item) {
            $product = $item->product;
            if ($product) {
                $productService->decreaseStock($product, $item->quantity);
            }
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createOrder(int $user_id, float $totalPrice, array $data): Order
    {
        return Order::create([
            'user_id' => $user_id,
            'address_id' => $data['address_id'] ?? null,
            'order_date' => $data['order_date'] ?? now(),
            'total_amount' => $totalPrice,
            'status' => $data['status'] ?? OrderStatus::PENDING,
            'coupon_id' => $data['coupon_id'] ?? null,
        ]);
    }

    private function createOrderItems(Order $order, Cart $cart): void
    {
        foreach ($cart->items as $item) {
            $product = $item->product;
            if ($product) {
                $unitPrice = $product->getDiscountedPrice() ?? $product->price;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                ]);
            }
        }
    }

    protected function calculateTotal(Cart $cart): float
    {
        $totalCents = 0;
        foreach ($cart->items as $item) {
            $product = $item->product;
            if ($product) {
                $unitPrice = $product->getDiscountedPrice() ?? $product->price; // float
                // Convert to integer cents to avoid floating point drift
                $unitCents = (int) round($unitPrice * 100);
                $totalCents += $unitCents * $item->quantity;
            }
        }
        return $totalCents / 100; // back to float with 2 decimals max of precision
    }

    protected function calculateTotalWithCoupon(Cart $cart, ?float $couponPercentage): float
    {
        $total = $this->calculateTotal($cart); // float representation (already aggregated)

        if ($couponPercentage !== null && $couponPercentage > 0) {
            // Work in cents for discount as well
            $totalCents = (int) round($total * 100);
            $discountCents = (int) round($totalCents * ($couponPercentage / 100));
            $totalCents -= $discountCents;
            $total = $totalCents / 100;
        }

        // Final rounding once
        $total = round($total, 2);
        $this->validateTotalAmount($total);
        return $total;
    }

    protected function validateTotalAmount(float $total): void
    {
        // Validation errors are client-related; use 422
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

    protected function getCartForUser(int $user_id): Cart
    {
        $user = User::find($user_id);
        if (!$user) {
            throw new ApiException('User not found.', null, 404);
        }

        $cart = $user->cart;
        if (!$cart) {
            throw new ApiException('No cart found for the authenticated user.', null, 404);
        }
        return $cart;
    }

    public function cancelOrder(Order $order, ProductService $productService): Order
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product) {
                $productService->increaseStock($product, $item->quantity);
            }
        }
        $order->status = OrderStatus::CANCELED;
        $order->save();
        return $order;
    }
}
