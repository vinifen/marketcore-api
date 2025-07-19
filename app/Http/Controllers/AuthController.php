<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Services\AuthService;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request, AuthService $authService)
    {
        $response = $authService->register($request->validated());
        return ApiResponse::success($response, 201);
    }

    public function login(LoginUserRequest $request, AuthService $authService)
    {
        $response = $authService->login($request->validated());
        return ApiResponse::success($response, 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::success(['message' => 'Logout successful']);
    }
}
