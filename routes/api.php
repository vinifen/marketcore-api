<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Responses\ApiResponse;

Route::get('/', function () {
    return ApiResponse::success([
        'message' => 'Welcome to the Marketcore API',
        'docs' => $_ENV['APP_URL'] . ':' . $_ENV['WEB_SERVER_PORT'] . '/api/documentation',
        'github' => "https://github.com/vinifen/marketcore-api",
    ], 200);
});

Route::post('/register', [AuthController::class, 'registerClient']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/register/mod', [AuthController::class, 'registerMod']);
    Route::apiResource('users', UserController::class);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('tests', TestController::class);
    Route::get('/users/{user}/tests', [TestController::class, 'index']);
});
