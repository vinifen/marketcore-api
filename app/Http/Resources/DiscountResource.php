<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'product_id' => $this->resource->product_id,
            'description' => $this->resource->description,
            'startDate' => $this->resource->startDate,
            'endDate' => $this->resource->endDate,
            'discountPercentage' => $this->resource->discountPercentage,
            'product' => $this->whenLoaded('product', fn () => $this->resource->product?->name),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}