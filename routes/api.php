<?php

use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\Catalog\CategoryController;
use App\Http\Controllers\Catalog\ProductController;
use App\Http\Controllers\Catalog\DiscountController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\UserController;
use App\Http\Responses\ApiResponse;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;

Route::get('/', function () {
    return ApiResponse::success([
        'message' => 'Welcome to the Marketcore API',
        'docs' => $_ENV['APP_URL'] . ':' . $_ENV['WEB_SERVER_PORT'] . '/api/documentation',
        'github' => "https://github.com/vinifen/marketcore-api",
    ], 200);
});

Route::post('/register', [AuthController::class, 'registerClient']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::get('/discounts', [DiscountController::class, 'index']);
Route::get('/discounts/{discount}', [DiscountController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/register/mod', [AuthController::class, 'registerMod']);

    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/restore', [UserController::class, 'restore']);
    Route::delete('users/{user}/force-delete', [UserController::class, 'forceDelete']);

    Route::apiResource('addresses', AddressController::class);
    Route::post('addresses/{address}/restore', [AddressController::class, 'restore']);
    Route::delete('addresses/{address}/force-delete', [AddressController::class, 'forceDelete']);

    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::post('products/{product}/restore', [ProductController::class, 'restore']);
    Route::delete('products/{product}/force-delete', [ProductController::class, 'forceDelete']);

    Route::apiResource('discounts', DiscountController::class)->except(['index', 'show']);
    Route::post('discounts/{discount}/restore', [DiscountController::class, 'restore']);
    Route::delete('discounts/{discount}/force-delete', [DiscountController::class, 'forceDelete']);

    Route::apiResource('cart', CartController::class)->only(['index', 'show']);
    Route::delete('cart/{cart}/clear', [CartController::class, 'clear']);

    Route::apiResource('cart-items', CartItemController::class);
    Route::delete('cart-items/{cartItem}/remove-one', [CartItemController::class, 'removeOne']);

    Route::apiResource('coupons', CouponController::class);
    Route::post('coupons/{coupon}/restore', [CouponController::class, 'restore']);
    Route::delete('coupons/{coupon}/force-delete', [CouponController::class, 'forceDelete']);


    Route::apiResource('order', OrderController::class);
    Route::post('order/{order}/restore', [OrderController::class, 'restore']);
    Route::delete('order/{order}/force-delete', [OrderController::class, 'forceDelete']);
    Route::put('order/{order}/status', [OrderController::class, 'updateStatus']);
    Route::post('order/{order}/cancel', [OrderController::class, 'cancel']);

    Route::apiResource('order-items', OrderItemController::class);
    Route::post('order-items/{orderItem}/restore', [OrderItemController::class, 'restore']);
    Route::delete('order-items/{orderItem}/force-delete', [OrderItemController::class, 'forceDelete']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
