<?php

namespace App\Http\Controllers;

use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Http\Responses\ApiResponse;
use App\Models\Address;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Address::class);
        $addresses = Address::with('user')->get();
        return ApiResponse::success(AddressResource::collection($addresses));
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $this->authorize('create', Address::class);
        $address = Address::create($request->validated());
        $address->load('user');
        return ApiResponse::success(new AddressResource($address), 201);
    }

    public function show(int $id): JsonResponse
    {
        $address = $this->findModelOrFail(Address::class, $id);
        $this->authorize('view', $address);
        $address->load('user');
        return ApiResponse::success(new AddressResource($address));
    }

    public function update(UpdateAddressRequest $request, int $id): JsonResponse
    {
        $address = $this->findModelOrFail(Address::class, $id);
        $this->authorize('update', $address);
        $address->update($request->validated());
        $address->load('user');
        return ApiResponse::success(new AddressResource($address));
    }

    public function destroy(int $id): JsonResponse
    {
        $address = $this->findModelOrFail(Address::class, $id);
        $this->authorize('delete', $address);

        $address->delete();
        return ApiResponse::success(null, 204);
    }

    public function restore(int $id): JsonResponse
    {
        $address = $this->findModelTrashedOrFail(Address::class, $id);
        $this->authorize('restore', $address);

        $address->restore();
        $address->load('user');

        return ApiResponse::success(new AddressResource($address));
    }

    public function forceDelete(int $id): JsonResponse
    {
        $address = $this->findModelTrashedOrFail(Address::class, $id);
        $this->authorize('forceDelete', $address);

        $address->forceDelete();

        return ApiResponse::success(null, 204);
    }
}
