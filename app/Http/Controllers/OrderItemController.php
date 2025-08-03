<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderItemResource;
use App\Http\Responses\ApiResponse;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;

class OrderItemController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', OrderItem::class);
        $orderItems = OrderItem::with(['order', 'product'])->get();
        return ApiResponse::success(OrderItemResource::collection($orderItems));
    }

    public function show(int $id): JsonResponse
    {
        $orderItem = $this->findModelOrFail(OrderItem::class, $id);
        $this->authorize('view', $orderItem);
        $orderItem->load(['order', 'product']);
        return ApiResponse::success(new OrderItemResource($orderItem));
    }
}
