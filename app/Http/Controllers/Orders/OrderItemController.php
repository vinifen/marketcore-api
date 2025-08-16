<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\OrderItem\StoreOrderItemRequest;
use App\Http\Requests\Orders\OrderItem\UpdateOrderItemRequest;
use App\Http\Resources\Orders\OrderItemResource;
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

    public function store(StoreOrderItemRequest $request): JsonResponse
    {
        $this->authorize('create', OrderItem::class);
        $orderItem = OrderItem::create($request->validated());
        $orderItem->load('product');
        return ApiResponse::success(new OrderItemResource($orderItem), 201);
    }

    public function update(UpdateOrderItemRequest $request, int $id): JsonResponse
    {
        $orderItem = $this->findModelOrFail(OrderItem::class, $id);
        $this->authorize('update', $orderItem);
        $orderItem->update($request->validated());
        $orderItem->load('product');
        return ApiResponse::success(new OrderItemResource($orderItem));
    }

    public function destroy(int $id): JsonResponse
    {
        $orderItem = $this->findModelOrFail(OrderItem::class, $id);
        $this->authorize('delete', $orderItem);
        $orderItem->delete();
        return ApiResponse::success(null, 200);
    }

    public function forceDelete(int $id): JsonResponse
    {
        $orderItem = $this->findModelOrFailWithTrashed(OrderItem::class, $id);
        $this->authorize('forceDelete', $orderItem);
        $orderItem->forceDelete();
        return ApiResponse::success(null, 200);
    }

    public function restore(int $id): JsonResponse
    {
        $orderItem = $this->findModelTrashedOrFail(OrderItem::class, $id);
        $this->authorize('restore', $orderItem);
        /** @var OrderItem $orderItem */
        $orderItem->restore();
        $orderItem->load('product');
        return ApiResponse::success(new OrderItemResource($orderItem));
    }
}
