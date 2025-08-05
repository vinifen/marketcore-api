<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Discount\StoreDiscountRequest;
use App\Http\Requests\Discount\UpdateDiscountRequest;
use App\Http\Resources\DiscountResource;
use App\Http\Responses\ApiResponse;
use App\Models\Discount;
use Illuminate\Http\JsonResponse;

class DiscountController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Discount::class);

        $discounts = Discount::with('product')->get();
        return ApiResponse::success(DiscountResource::collection($discounts));
    }

    public function store(StoreDiscountRequest $request): JsonResponse
    {
        $this->authorize('create', Discount::class);

        $discount = Discount::create($request->validated());
        $discount->load('product');
        return ApiResponse::success(new DiscountResource($discount), 201);
    }

    public function show(int $id): JsonResponse
    {
        $discount = $this->findModelOrFail(Discount::class, $id);
        $this->authorize('view', $discount);

        $discount->load('product');
        return ApiResponse::success(new DiscountResource($discount));
    }

    public function update(UpdateDiscountRequest $request, int $id): JsonResponse
    {
        $discount = $this->findModelOrFail(Discount::class, $id);
        $this->authorize('update', $discount);

        $discount->update($request->validated());
        $discount->load('product');
        return ApiResponse::success(new DiscountResource($discount));
    }

    public function destroy(int $id): JsonResponse
    {
        $discount = $this->findModelOrFail(Discount::class, $id);
        $this->authorize('delete', $discount);

        $discount->delete();
        return ApiResponse::success(null, 204);
    }

    public function restore(int $id): JsonResponse
    {
        $discount = $this->findModelTrashedOrFail(Discount::class, $id);
        $this->authorize('restore', $discount);

        $discount->restore();
        $discount->load('product');

        return ApiResponse::success(new DiscountResource($discount));
    }

    public function forceDelete(int $id): JsonResponse
    {
        $discount = $this->findModelTrashedOrFail(Discount::class, $id);
        $this->authorize('forceDelete', $discount);

        $discount->forceDelete();

        return ApiResponse::success(null, 204);
    }
}
