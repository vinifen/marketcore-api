<?php

namespace App\Http\Controllers\Coupons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coupons\StoreCouponRequest;
use App\Http\Requests\Coupons\UpdateCouponRequest;
use App\Http\Resources\Coupons\CouponResource;
use App\Http\Responses\ApiResponse;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;

class CouponController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Coupon::class);
        $coupons = Coupon::all();
        return ApiResponse::success(CouponResource::collection($coupons));
    }

    public function store(StoreCouponRequest $request): JsonResponse
    {
        $this->authorize('create', Coupon::class);

        $data = $request->validated();
        if (empty($data['start_date'])) {
            $data['start_date'] = now()->toDateString();
        }

        $coupon = Coupon::create($data);
        return ApiResponse::success(new CouponResource($coupon), 201);
    }

    public function show(int $id): JsonResponse
    {
        $coupon = $this->findModelOrFail(Coupon::class, $id);
        $this->authorize('view', $coupon);
        return ApiResponse::success(new CouponResource($coupon));
    }

    public function update(UpdateCouponRequest $request, int $id): JsonResponse
    {
        $coupon = $this->findModelOrFail(Coupon::class, $id);
        $this->authorize('update', $coupon);

        $coupon->update($request->validated());
        return ApiResponse::success(new CouponResource($coupon));
    }

    public function destroy(int $id): JsonResponse
    {
        $coupon = $this->findModelOrFail(Coupon::class, $id);
        $this->authorize('delete', $coupon);

        $coupon->delete();
        return ApiResponse::success(null, 200);
    }

    public function restore(int $id): JsonResponse
    {
        /** @var Coupon $coupon */
        $coupon = $this->findModelTrashedOrFail(Coupon::class, $id);
        $this->authorize('restore', $coupon);

        $coupon->restore();

        return ApiResponse::success(new CouponResource($coupon));
    }

    public function forceDelete(int $id): JsonResponse
    {
        $coupon = $this->findModelOrFailWithTrashed(Coupon::class, $id);
        $this->authorize('forceDelete', $coupon);

        $coupon->forceDelete();

        return ApiResponse::success(null, 200);
    }
}
