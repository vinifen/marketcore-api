<?php

namespace App\Services;

use App\Models\CartItem;
use App\Exceptions\ApiException;

class CartItemService
{
    public function updateQuantity(
        CartItem $cartItem,
        int $quantity,
        ProductService $productService
    ): CartItem {
        $product = $cartItem->product;
        if ($product) {
            $productService->ensureProductHasStock($product, $quantity);
        }
        $cartItem->quantity = $quantity;
        if (!$cartItem->save()) {
            throw new ApiException('Failed to update cart item.', null, 500);
        }
        return $cartItem;
    }

    public function removeOne(CartItem $cartItem): void
    {
        if ($cartItem->quantity <= 1) {
            $cartItem->forceDelete();
        } else {
            $cartItem->quantity -= 1;
            $cartItem->save();
        }
    }
}
