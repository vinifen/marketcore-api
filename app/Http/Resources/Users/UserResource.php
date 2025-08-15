<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\User $resource
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'email' => $this->resource->email,
            'role' => $this->resource->role,
            'cart_id' => $this->resource->cart->id,
            'addresses' => $this->resource->addresses->map(function ($address) {
                return [
                    'id' => $address->id,
                    'street' => $address->street,
                    'city' => $address->city,
                    'state' => $address->state,
                    'postal_code' => $this->resource->postal_code,
                ];
            }),
        ];
    }
}
