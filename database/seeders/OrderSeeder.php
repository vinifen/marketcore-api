<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() === 0) {
            User::factory()->count(5)->create();
        }
        if (Address::count() === 0) {
            Address::factory()->count(5)->create();
        }
        if (Product::count() === 0) {
            Product::factory()->count(10)->create();
        }

        $statuses = [
            OrderStatus::PENDING,
            OrderStatus::PROCESSING,
            OrderStatus::COMPLETED,
            OrderStatus::CANCELED,
        ];

        $users = User::inRandomOrder()->take(5)->get();
        $addresses = Address::inRandomOrder()->take(5)->get();
        $products = Product::inRandomOrder()->take(20)->get();

        foreach (range(1, 12) as $i) {
            /** @var User $user */
            $user = $users->random();
            $address = $addresses->random();

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'order_date' => now()->subDays(rand(0, 30)),
                'status' => $statuses[array_rand($statuses)],
                'total_amount' => 0,
            ]);

            $itemsProducts = $products->random(rand(1, min(5, $products->count())));
            if ($itemsProducts instanceof Product) {
                $itemsProducts = collect([$itemsProducts]);
            }

            $total = 0.0;
            /** @var Collection<int, Product> $itemsProducts */
            foreach ($itemsProducts as $product) {
                $quantity = rand(1, 4);
                $unit = $product->price;
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unit,
                ]);
                $total += $quantity * $unit;
            }

            $order->update(['total_amount' => round($total, 2)]);
        }
    }
}
