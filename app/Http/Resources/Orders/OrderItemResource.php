<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'order_id' => $this->resource->order_id,
            'product_id' => $this->resource->product_id,
            'product_name' => $this->whenLoaded('product', fn() => $this->resource->product->name),
            'quantity' => $this->resource->quantity,
            'unit_price' => (float) $this->resource->unit_price,
            'total_price' => (float) round($this->resource->quantity * $this->resource->unit_price, 2),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
