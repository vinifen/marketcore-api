<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\DestroyUserRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Actions\UpdateUserAction;
use App\Exceptions\ApiException;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return ApiResponse::success(User::all());
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            throw new ApiException('User not found.', null, 404);
        }

        $this->authorize('show', $user);
        return ApiResponse::success($user);
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
        UpdateUserAction $updateUserAction
    ): JsonResponse {
        $this->authorize('update', $user);

        $result = $updateUserAction->execute($user, $request->validated());

        return ApiResponse::success($result);
    }

    public function destroy(
        DestroyUserRequest $request,
        User $user,
        AuthService $authService
    ): JsonResponse {
        $this->authorize('delete', $user);
        $password = (string) $request->input('password');
        $authService->validatePassword($user->password, $password);
        $user->delete();
        return ApiResponse::success(['message' => 'User deleted successfully.']);
    }
}
