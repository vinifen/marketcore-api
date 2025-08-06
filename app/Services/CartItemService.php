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
        $product = $this->findProduct($data['product_id']);
        $quantity = $data['quantity'] ?? 1;

        $productService->ensureProductHasStock($product, $quantity);

        $existing = $this->findExistingCartItem($data['cart_id'], $data['product_id']);

        if ($existing) {
            return $this->updateExistingItem($existing, $quantity, $productService, $product);
        }

        $data['quantity'] = $quantity;

        return $this->createCartItem($data);
    }

    protected function findProduct(int $productId): Product
    {
        $product = Product::find($productId);
        if (!$product) {
            throw new ApiException('Product not found.', null, 404);
        }
        return $product;
    }

    protected function findExistingCartItem(int $cartId, int $productId): ?CartItem
    {
        return CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();
    }

    protected function updateExistingItem(
        CartItem $existing,
        int $quantity,
        ProductService $productService,
        Product $product
    ): CartItem {
        $newQuantity = $existing->quantity + $quantity;
        $productService->ensureProductHasStock($product, $newQuantity);

        $existing->quantity = $newQuantity;
        if (!$existing->save()) {
            throw new ApiException('Failed to update cart item.', null, 500);
        }
        return $existing;
    }

    protected function createCartItem(array $data): CartItem
    {
        $result = CartItem::create($data);
        if (!$result) {
            throw new ApiException('Failed to create cart item.', null, 500);
        }
        return $result;
    }

    public function updateQuantity(
        CartItem $cartItem,
        int $quantity,
        ProductService $productService
    ): CartItem {
        $productService->ensureProductHasStock($cartItem->product, $quantity);
        $cartItem->quantity = $quantity;
        if (!$cartItem->save()) {
            throw new ApiException('Failed to update cart item.', null, 500);
        }
        return $cartItem;
    }

    public function removeOne(CartItem $cartItem): void
    {
        if ($cartItem->quantity <= 1) {
            $this->forceDelete($cartItem);
        } else {
            $cartItem->quantity -= 1;
            $cartItem->save();
        }
    }

    public function forceDelete(CartItem $cartItem): void
    {
        if (!$cartItem->forceDelete()) {
            throw new ApiException('Failed to delete cart item.', null, 500);
        }
    }
}
