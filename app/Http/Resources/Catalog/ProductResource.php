<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'category_id' => $this->resource->category_id,
            'name' => $this->resource->name,
            'stock' => $this->resource->stock,
            'price' => (float) $this->resource->price,
            'category' => $this->whenLoaded('category', fn () => $this->resource->category?->name),
            'image_url' => $this->resource->image_url,
        ];
    }
}
