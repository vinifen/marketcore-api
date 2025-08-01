<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Database\Seeder;

class CartItemSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::orderBy('id')->take(5)->get();
        $products = Product::all();

        foreach ($users as $user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $randomProducts = $products->random(4);

            foreach ($randomProducts as $product) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5),
                    'unit_price' => $product->price,
                ]);
            }
        }
    }
}