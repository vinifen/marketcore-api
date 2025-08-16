<?php

namespace App\Http\Resources\Orders;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'user_id' => $this->resource->user_id,
            'address_id' => $this->resource->address_id,
            'coupon_id' => $this->resource->coupon_id,
            'order_date' => $this->resource->order_date,
            'total_amount' => (float) $this->resource->total_amount,
            'status' => $this->resource->status,
            'items_ids' => $this->whenLoaded('items', fn() => $this->resource->items->pluck('id')),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
