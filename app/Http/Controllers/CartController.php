<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Http\Responses\ApiResponse;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Cart::class);
        $carts = Cart::with('user')->get();
        return ApiResponse::success(CartResource::collection($carts));
    }

    public function show(int $id): JsonResponse
    {
        $cart = $this->findModelOrFail(Cart::class, $id);
        $this->authorize('view', $cart);
        $cart->load('user');
        return ApiResponse::success(new CartResource($cart));
    }

    public function clear(int $id): JsonResponse
    {
        /** @var Cart $cart */
        $cart = $this->findModelOrFail(Cart::class, $id);
        $this->authorize('clear', $cart);
        $cart->items()->forceDelete();
        return ApiResponse::success(['message' => 'Cart cleared successfully']);
    }

    // public function store(StoreCartRequest $request)
    // {
    //     //
    // }

    // public function update(UpdateCartRequest $request, Cart $cart)
    // {
    //     //
    // }

    // public function destroy(Cart $cart)
    // {
    //     //
    // }
}
