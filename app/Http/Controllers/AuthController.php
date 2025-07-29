<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Services\AuthService;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function registerClient(StoreUserRequest $request, AuthService $authService): JsonResponse
    {
        $response = $authService->registerClient($request->validated(), app(UserService::class));
        return ApiResponse::success($response, 201);
    }

    public function registerMod(StoreUserRequest $request, AuthService $authService): JsonResponse
    {
        $this->authorize('create', User::class);
        $response = $authService->registerMod($request->validated(), app(UserService::class));
        return ApiResponse::success($response, 201);
    }


    public function login(LoginUserRequest $request, AuthService $authService): JsonResponse
    {
        $response = $authService->login($request->validated());
        return ApiResponse::success($response);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return ApiResponse::success(['message' => 'Logout successful.']);
    }
}
