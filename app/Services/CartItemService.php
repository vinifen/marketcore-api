<?php

namespace App\Services;

use App\Models\CartItem;
use App\Exceptions\ApiException;

class CartItemService
{
    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data, ProductService $productService): CartItem
    {
        $productService->setProductById($data['product_id']);
        $quantity = $data['quantity'] ?? 1;
        $productService->checkProductStock($quantity);

        $existing = CartItem::where('cart_id', $data['cart_id'])
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existing) {
            $newQuantity = $existing->quantity + $quantity;
            $productService->checkProductStock($newQuantity);

            $existing->quantity = $newQuantity;
            $existing->unit_price = $productService->getProductPrice();
            if (!$existing->save()) {
                throw new ApiException('Failed to update cart item.', null, 500);
            }
            return $existing;
        }

        $data['quantity'] = $quantity;
        $data['unit_price'] = $productService->getProductPrice();
        $result = CartItem::create($data);
        return $result;
    }

    public function updateQuantity(CartItem $cartItem, int $quantity, ProductService $productService): CartItem
    {
        $productService->setProductById($cartItem->product_id);
        $productService->checkProductStock($quantity);
        $cartItem->quantity = $quantity;
        $cartItem->unit_price = $productService->getProductPrice();
        $result = $cartItem->save();
        if (!$result) {
            throw new ApiException('Failed to update cart item.', null, 500);
        }
        return $cartItem;
    }
}
