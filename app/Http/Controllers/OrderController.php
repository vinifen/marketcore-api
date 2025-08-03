<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::all();

        return ApiResponse::success(OrderResource::collection($orders));
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $data = $request->validated();

        if (!empty($data['coupon_code'])) {
            $coupon = Coupon::where('code', $data['coupon_code'])->first();
            $data['coupon_id'] = $coupon?->id;
            unset($data['coupon_code']);
        }

        $user = User::find($data['user_id']);
        $data['user_email'] = $user?->email;

        $order = Order::create($data);

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

    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('forceDelete', $order);

        $order->delete();

        return ApiResponse::success(null, 204);
    }
}
