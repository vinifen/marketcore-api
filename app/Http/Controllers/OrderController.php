<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Requests\Order\UpdateStatusOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::all();

        return ApiResponse::success(OrderResource::collection($orders));
    }

    public function store(StoreOrderRequest $request, OrderService $orderService): JsonResponse
    {
        $this->authorize('create', Order::class);
        $user = User::find($request->user_id);

        if (!$user instanceof User) {
            return ApiResponse::error('User not found.', 404);
        }

        $order = $orderService->store(
            $request->products,
            $user->id,
            app(ProductService::class)
        );

        return ApiResponse::success(new OrderResource($order), 201);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order->load(['user', 'address', 'coupon']);

        return ApiResponse::success(new OrderResource($order));
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        $order->update($request->validated());

        return ApiResponse::success(new OrderResource($order));
    }

    public function updateStatus(Order $order, UpdateStatusOrderRequest $request): JsonResponse
    {
        $this->authorize('update', $order);

        $data = $request->validated();
        if (isset($data['status'])) {
            $order->status = $data['status'];
            $order->save();
        }

        return ApiResponse::success(new OrderResource($order));
    }

    public function cancel(Order $order, OrderService $orderService): JsonResponse
    {
        $this->authorize('cancel', $order);

        $order = $orderService->cancelOrder($order, app(ProductService::class));

        return ApiResponse::success(new OrderResource($order));
    }

    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        $order->delete();

        return ApiResponse::success(null, 204);
    }

    public function restore(Order $order): JsonResponse
    {
        $this->authorize('restore', $order);

        $order->restore();
        $order->load(['user', 'address', 'coupon']);

        return ApiResponse::success(new OrderResource($order));
    }

    public function forceDelete(Order $order): JsonResponse
    {
        $this->authorize('forceDelete', $order);

        $order->forceDelete();

        return ApiResponse::success(null, 204);
    }
}
