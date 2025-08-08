<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
            throw new ApiException(
                "Cannot decrease stock by {$quantity}. Only {$product->stock} units available for product '{$product->name}'.",
                null,
                422
            );
        }
        $product->stock -= $quantity;
        $product->save();
    }

    public function increaseStock(Product $product, int $quantity): void
    {
        $product->stock += $quantity;
        $product->save();
    }

    private function uploadImage(UploadedFile $image): string
    {
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        
        $path = $image->storeAs('products', $filename, 'public');
        
        return Storage::url($path);
    }

    public function deleteImage(?string $imageUrl): void
    {
        if (!$imageUrl) {
            return;
        }

        $path = str_replace('/storage/', '', parse_url($imageUrl, PHP_URL_PATH));
        
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function updateProduct(Product $product, array $data, ?UploadedFile $image = null): Product
    {
        $oldImageUrl = $product->image_url;

        if ($image) {

            $data['image_url'] = $this->uploadImage($image);

            if ($oldImageUrl) {
                $this->deleteImage($oldImageUrl);
            }
        }

        $product->update($data);
        return $product->fresh();
    }
}
