<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
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
        $cart = $this->getCurrentCart();

        $coupon = $this->getCouponFromData($data);
        $data['coupon_id'] = $coupon?->id;

        $totalPrice = $this->calculateTotalWithCoupon($cart, $coupon?->discount_percentage);

        $this->decreaseProductsStock($cart, $productService);

        $order = $this->createOrder($user_id, $totalPrice, $data['coupon_id']);

        $this->createOrderItems($order, $cart);

        $this->clearCart($cart);

        return $order;
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

    private function createOrder(int $user_id, float $totalPrice, ?int $coupon_id): Order
    {
        return Order::create([
            'user_id' => $user_id,
            'total_amount' => $totalPrice,
            'status' => OrderStatus::PENDING,
            'coupon_id' => $coupon_id,
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

    private function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
        $cart->save();
    }

    protected function calculateTotal(Cart $cart): float
    {
        $total = 0.0;
        foreach ($cart->items as $item) {
            $product = $item->product;
            if ($product) {
                $unitPrice = $product->getDiscountedPrice() ?? $product->price;
                $total += $unitPrice * $item->quantity;
            }
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
