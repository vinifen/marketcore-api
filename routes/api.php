<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('tests', TestController::class);
    Route::get('/users/{user}/tests', [TestController::class, 'index']);
});


