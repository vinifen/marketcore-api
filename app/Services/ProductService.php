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
        $this->product = Product::findOrFail($productId);
    }

    public function checkProductStock(int $quantity): void
    {
        if ($this->product === null) {
            throw new ApiException('Product not found.', null, 404);
        }
        if ($this->product->stock < $quantity) {
            throw new ApiException('Insufficient stock.', null, 422);
        }
    }

    public function getProductPrice(): float
    {
        if ($this->product === null) {
            throw new ApiException('Product not set.', null, 400);
        }
        return $this->product->price;
    }
}
