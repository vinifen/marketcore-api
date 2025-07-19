<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Actions\UpdateUserAction;

class UserController extends Controller
{
    public function index()
    {
        return ApiResponse::success(User::all());
    }

    public function show(Request $request)
    {
        $user = $request->user();
        return ApiResponse::success($user);
    }

    public function update(UpdateUserRequest $request, UpdateUserAction $updateUserAction)
    {
        $result = $updateUserAction->execute($request->user(), $request->validated());
        return ApiResponse::success($result, 200);
    }

    public function destroy(Request $request)
    {
        AuthService::validatePassword($request->user(), $request->input('password'));
        $request->user()->delete();
        return ApiResponse::success(['message' => 'User deleted successfully']);
    }
}
