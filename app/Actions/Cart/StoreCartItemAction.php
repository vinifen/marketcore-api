<?php

namespace App\Actions\Cart;

use App\Exceptions\ApiException;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\ProductService;

class StoreCartItemAction
{
    public function __construct(private ProductService $productService)
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function execute(array $data): CartItem
    {
        $product = $this->findProduct($data['product_id']);
        $quantity = $data['quantity'] ?? 1;

        $this->productService->ensureProductHasStock($product, $quantity);

        $existing = $this->findExistingCartItem($data['cart_id'], $data['product_id']);

        if ($existing) {
            return $this->updateExistingItem($existing, $quantity, $product);
        }

        $data['quantity'] = $quantity;

        $result = CartItem::create($data);
        return $result;
    }

    private function findProduct(int $productId): Product
    {
        $product = Product::find($productId);
        if (!$product) {
            throw new ApiException('Product not found.', null, 404);
        }
        return $product;
    }

    private function findExistingCartItem(int $cartId, int $productId): ?CartItem
    {
        return CartItem::where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();
    }

    private function updateExistingItem(
        CartItem $existing,
        int $quantity,
        Product $product
    ): CartItem {
        $newQuantity = $existing->quantity + $quantity;
        $this->productService->ensureProductHasStock($product, $newQuantity);

        $existing->quantity = $newQuantity;
        if (!$existing->save()) {
            throw new ApiException('Failed to update cart item.', null, 500);
        }
        return $existing;
    }
}
