<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Services\AuthService;
use App\Http\Responses\ApiResponse;

class AuthController extends Controller
{
    use ApiResponse;
    
    public function register(StoreUserRequest $request, AuthService $authService)
    {
        $response = $authService->register($request->validated());
        return $this->response_success($response, 201);
    }

    public function login(LoginUserRequest $request, AuthService $authService)
    {
        $response = $authService->login($request->validated());
        return $this->response_success($response, 200);
    }
}
