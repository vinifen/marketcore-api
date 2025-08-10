<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        if (Order::count() === 0) {
            return;
        }
        if (Product::count() === 0) {
            return;
        }

        $products = Product::inRandomOrder()->get();


        Order::query()->inRandomOrder()
            ->take((int) (Order::count() * 0.5))
            ->get()
            ->each(function (Order $order) use ($products) {
                $count = min(max(1, rand(1, 2)), $products->count());
                $additionalProducts = $products->random($count);
                if ($additionalProducts instanceof Product) {
                    $additionalProducts = collect([$additionalProducts]);
                }

                $added = 0.0;
                foreach ($additionalProducts as $product) {
                    if (! $product instanceof Product) {
                        continue;
                    }

                    if ($order->items()->where('product_id', $product->id)->exists()) {
                        continue;
                    }
                    $qty = rand(1, 3);
                    $unit = $product->price;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $unit,
                    ]);
                    $added += $qty * $unit;
                }

                if ($added > 0) {
                    $order->update(['total_amount' => round($order->total_amount + $added, 2)]);
                }
            });
    }
}
