<?php

namespace App\Http\Resources;

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
            'total_amount' => $this->resource->total_amount,
            'status' => $this->resource->status,
            'user' => new UserResource($this->whenLoaded('user')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'coupon' => new CouponResource($this->whenLoaded('coupon')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
