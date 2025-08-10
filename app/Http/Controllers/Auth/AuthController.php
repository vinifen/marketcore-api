<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Enums\UserRole;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Users\User\StoreUserRequest;
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
        $response = $authService->register(
            $request->validated(),
            UserRole::CLIENT,
            app(UserService::class)
        );
        return ApiResponse::success($response, 201);
    }

    public function registerMod(StoreUserRequest $request, AuthService $authService): JsonResponse
    {
        $this->authorize('create', User::class);
        $response = $authService->register(
            $request->validated(),
            UserRole::MODERATOR,
            app(UserService::class)
        );
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
