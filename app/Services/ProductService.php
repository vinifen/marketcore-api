<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Product;

class ProductService
{
    private ?Product $product = null;

    /**
     * @param int $productId
     * @return void
     */
    public function setProductById(int $productId): void
    {
        $this->product = Product::find($productId);
        if (!$this->product) {
            throw new ApiException('Product not found.', null, 404);
        }
    }

    public function checkProductStock(int $quantity): void
    {
        if ($this->product === null) {
            throw new ApiException('Product not set.', null, 500);
        }
        if ($this->product->stock < $quantity) {
            throw new ApiException('Insufficient stock.', null, 422);
        }
    }

    public function getProductPrice(): float
    {
        if ($this->product === null) {
            throw new ApiException('Product not set.', null, 500);
        }
        return $this->product->price;
    }

    public function decreaseStock(Product $product, int $quantity): void
    {
        if ($product->stock < $quantity) {
            throw new ApiException('Stock cannot be negative', null, 422);
        }

        if($product->stock === $quantity) {
            $this->forceDelete($product);
            return;
        }
        $product->stock -= $quantity;
        $product->save();
    }

    public function increaseStock(Product $product, int $quantity): void
    {
        $product->stock += $quantity;
        $product->save();
    }

    public function forceDelete(Product $product): void
    {
        if ($product->stock > 0) {
            throw new ApiException('Cannot delete product with stock.', null, 422);
        }
        $product->delete();
    }
}
