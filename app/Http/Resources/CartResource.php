<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $items = $this->resource->items->map(function ($item) {
            $unitPrice = $item->product->price ?? 0.0;
            $unitPriceDiscounted = optional($item->product)->getDiscountedPrice();
            $quantity = $item->quantity;

            $totalPrice = round($unitPrice * $quantity, 2);
            $totalPriceDiscounted = $unitPriceDiscounted !== null ? round($unitPriceDiscounted * $quantity, 2) : null;

            return [
                'product_id' => $item->product_id,
                'product_name' => optional($item->product)->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'unit_price_discounted' => $unitPriceDiscounted,
                'discount_value' => optional($item->product)->getTotalDiscountPercentage(),
                'total_price' => $totalPrice,
                'total_price_discounted' => $totalPriceDiscounted,
            ];
        });

        $cartTotal = $items->sum('total_price');
        $discountedItems = $items->filter(fn($item) => $item['total_price_discounted'] !== null);
        $cartTotalDiscounted = $discountedItems->isNotEmpty()
            ? $discountedItems->sum('total_price_discounted')
            : null;

        return [
            'id' => $this->resource->id,
            'user_id' => optional($this->resource->user)->id,
            'user_email' => optional($this->resource->user)->email,
            'user_name' => optional($this->resource->user)->name,
            'items' => $items,
            'cart_total' => round($cartTotal, 2),
            'cart_total_discounted' => $cartTotalDiscounted !== null ? round($cartTotalDiscounted, 2) : null,
        ];
    }
}
