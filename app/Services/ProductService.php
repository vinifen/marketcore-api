<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Product;

class ProductService
{

    public function ensureProductHasStock(Product $product, int $quantity): void
    {
        if ($product->stock < $quantity) {
            throw new ApiException('Insufficient stock.', null, 422);
        }
    }

    public function decreaseStock(Product $product, int $quantity): void
    {
        if ($product->stock < $quantity) {
            throw new ApiException('Insufficient stock.', null, 422);
        }
        $product->stock -= $quantity;
        $product->save();
    }

    public function increaseStock(Product $product, int $quantity): void
    {
        $product->stock += $quantity;
        $product->save();
    }
}
