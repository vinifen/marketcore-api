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
        $filename = (string) Str::uuid() . '.' . $image->getClientOriginalExtension();

        $path = $image->storeAs('products', $filename, 'public');
        if ($path === false) {
            throw new ApiException('Failed to store product image.', null, 500);
        }

        $storageUrl = Storage::url($path);

        return str_replace('/storage/', '/api/storage/', $storageUrl);
    }

    public function deleteImage(?string $imageUrl): void
    {
        if (!$imageUrl) {
            return;
        }

        $pathFromUrl = parse_url($imageUrl, PHP_URL_PATH);
        if (!is_string($pathFromUrl)) {
            return;
        }

        $path = ltrim(str_replace(['/api/storage/', '/storage/'], '', $pathFromUrl), '/');

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateProduct(
        Product $product,
        array $data,
        ?UploadedFile $image = null,
        ?bool $removeImage = false
    ): Product {
        $oldImageUrl = $product->image_url;

        if ($image) {
            $data['image_url'] = $this->uploadImage($image);

            if ($oldImageUrl) {
                $this->deleteImage($oldImageUrl);
            }
        } elseif ($removeImage) {
            if ($oldImageUrl) {
                $this->deleteImage($oldImageUrl);
            }
            $data['image_url'] = null;
        }

        unset($data['remove_image'], $data['image']);

        $product->update($data);
        $product->refresh();
        return $product;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createProduct(array $data, ?UploadedFile $image = null): Product
    {
        if ($image) {
            $data['image_url'] = $this->uploadImage($image);
        }

        unset($data['image']);

        $product = Product::create($data);
        return $product;
    }
}
