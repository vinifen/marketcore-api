<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'user_id' => optional($this->resource->cart->user)->id,
            'user_name' => optional($this->resource->cart->user)->name,
            'cart_id' => $this->resource->cart_id,
            'product_id' => $this->resource->product_id,
            'product_name' => optional($this->resource->product)->name,
            'quantity' => $this->resource->quantity,
            'unit_price' => $this->resource->unit_price,
            'total_price' => $this->resource->quantity * $this->resource->unit_price,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
