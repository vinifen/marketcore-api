<?php

namespace App\Services;

use App\Models\CartItem;
use App\Exceptions\ApiException;
use App\Models\Product;

class CartItemService
{
    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data, ProductService $productService): CartItem
    {
        $product = Product::find($data['product_id']);
        if (!$product) {
            throw new ApiException('Product not found.', null, 404);
        }

        $quantity = $data['quantity'] ?? 1;
        $productService->ensureProductHasStock($product, $quantity);

        $existing = CartItem::where('cart_id', $data['cart_id'])
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existing) {
            $newQuantity = $existing->quantity + $quantity;
            $productService->ensureProductHasStock($product, $newQuantity);

            $existing->quantity = $newQuantity;
            if (!$existing->save()) {
                throw new ApiException('Failed to update cart item.', null, 500);
            }
            return $existing;
        }

        $data['quantity'] = $quantity;
        $data['unit_price'] = $product->price;
        $result = CartItem::create($data);
        return $result;
    }

    public function updateQuantity(CartItem $cartItem, int $quantity, ProductService $productService): CartItem
    {
        $productService->ensureProductHasStock($cartItem->product, $quantity);
        $cartItem->quantity = $quantity;
        $cartItem->unit_price = $cartItem->product->price;
        $result = $cartItem->save();
        if (!$result) {
            throw new ApiException('Failed to update cart item.', null, 500);
        }
        return $cartItem;
    }

    public function removeOne(CartItem $cartItem): void
    {
        if ($cartItem->quantity <= 1) {
            $this->delete($cartItem);
        } else {
            $cartItem->quantity -= 1;
            $cartItem->save();
        }
    }

    public function delete(CartItem $cartItem): void
    {
        if (!$cartItem->delete()) {
            throw new ApiException('Failed to delete cart item.', null, 500);
        }
    }
}
