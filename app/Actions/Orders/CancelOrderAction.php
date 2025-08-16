<?php

namespace App\Actions\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\ProductService;

class CancelOrderAction
{
    public function execute(Order $order, ProductService $productService): Order
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product) {
                $productService->increaseStock($product, $item->quantity);
            }
        }
        $order->status = OrderStatus::CANCELED;
        $order->save();
        return $order;
    }
}
