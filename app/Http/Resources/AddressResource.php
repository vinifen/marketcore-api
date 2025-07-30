<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->resource->id,
            'user_id' => $this->resource->user_id,
            'street' => $this->resource->street,
            'number' => $this->resource->number,
            'complement' => $this->resource->complement,
            'city' => $this->resource->city,
            'state' => $this->resource->state,
            'postal_code' => $this->resource->postal_code,
            'country' => $this->resource->country,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
