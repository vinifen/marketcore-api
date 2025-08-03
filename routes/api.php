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
    Route::apiResource('addresses', AddressController::class);

    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::apiResource('discounts', DiscountController::class)->except(['index', 'show']);

    Route::apiResource('cart', CartController::class)->only(['index', 'show']);
    Route::delete('cart/{cart}/clear', [CartController::class, 'clear']);

    Route::apiResource('cart-items', CartItemController::class);
    Route::delete('cart-items/{cartItem}/remove-one', [CartItemController::class, 'removeOne']);

    Route::apiResource('coupons', CouponController::class);


    Route::apiResource('order', OrderController::class)->only(['index', 'show', 'store']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
