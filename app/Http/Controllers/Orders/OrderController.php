<?php

namespace App\Http\Controllers\Orders;

use App\Actions\Orders\CancelOrderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\Order\StoreOrderRequest;
use App\Http\Requests\Orders\Order\UpdateOrderRequest;
use App\Http\Requests\Orders\Order\UpdateStatusOrderRequest;
use App\Http\Resources\Orders\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use App\Actions\Orders\StoreOrderAction;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::with(['items'])->get();

        return ApiResponse::success(OrderResource::collection($orders));
    }

    public function store(StoreOrderRequest $request, StoreOrderAction $storeOrderAction): JsonResponse
    {
        $this->authorize('create', Order::class);

        $order = $storeOrderAction->execute(
            $request->validated(),
            $request->user_id,
            app(ProductService::class)
        );

        return ApiResponse::success(new OrderResource($order), 201);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order->load(['items']);

        return ApiResponse::success(new OrderResource($order));
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);
        $order->update($request->validated());
        $order->load(['items']);
        return ApiResponse::success(new OrderResource($order));
    }

    public function updateStatus(Order $order, UpdateStatusOrderRequest $request): JsonResponse
    {
        $this->authorize('updateStatus', $order);

        $data = $request->validated();
        if (isset($data['status'])) {
            $order->status = $data['status'];
            $order->save();
        }

        $order->load(['items']);
        return ApiResponse::success(new OrderResource($order));
    }

    public function cancel(int $id, CancelOrderAction $cancelOrderAction): JsonResponse
    {
        $order = $this->findModelOrFail(Order::class, $id);
        /** @var Order $order */
        $this->authorize('cancel', $order);
        $order = $cancelOrderAction->execute($order, app(ProductService::class));
        return ApiResponse::success(new OrderResource($order));
    }

    public function destroy(int $id): JsonResponse
    {
        $order = $this->findModelOrFail(Order::class, $id);
        $this->authorize('delete', $order);
        $order->delete();
        return ApiResponse::success(null, 200);
    }

    public function restore(int $id): JsonResponse
    {
        $order = $this->findModelTrashedOrFail(Order::class, $id);
        $this->authorize('restore', $order);
        /** @var Order $order */
        $order->restore();
        $order->load(['items']);

        return ApiResponse::success(new OrderResource($order));
    }

    public function forceDelete(int $id): JsonResponse
    {
        $order = $this->findModelOrFailWithTrashed(Order::class, $id);
        $this->authorize('forceDelete', $order);
        $order->forceDelete();
        return ApiResponse::success(null, 200);
    }
}
