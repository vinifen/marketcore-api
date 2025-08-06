<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\DestroyUserRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\UserService;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $users = User::all();

        return ApiResponse::success(UserResource::collection($users));
    }

    public function store(StoreUserRequest $request, UserService $userService): JsonResponse
    {
        $this->authorize('create', User::class);
        $result = $userService->store($request->validated());
        return ApiResponse::success(new UserResource($result), 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->findModelOrFail(User::class, $id);

        $this->authorize('view', $user);

        return ApiResponse::success(new UserResource($user));
    }

    public function update(
        UpdateUserRequest $request,
        int $id,
        UserService $userService,
    ): JsonResponse {
        /** @var \App\Models\User $user */
        $user = $this->findModelOrFail(User::class, $id);
        $this->authorize('update', $user);

        $result = $userService->update($user, $request->validated(), app(AuthService::class));

        return ApiResponse::success(new UserResource($result));
    }

    public function destroy(
        DestroyUserRequest $request,
        int $id,
        AuthService $authService
    ): JsonResponse {
        /** @var \App\Models\User $user */
        $user = $this->findModelOrFail(User::class, $id);
        $this->authorize('delete', $user);

        $password = (string) $request->input('password');
        $authService->validatePassword($user->password, $password);
        $user->delete();

        return ApiResponse::success(null, 204);
    }

    public function restore(int $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $this->findModelTrashedOrFail(User::class, $id);
        $this->authorize('restore', $user);

        $user->restore();

        return ApiResponse::success(new UserResource($user));
    }

    public function forceDelete(int $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $this->findModelOrFailWithTrashed(User::class, $id);
        $this->authorize('forceDelete', $user);

        $user->forceDelete();

        return ApiResponse::success(null, 204);
    }
}
