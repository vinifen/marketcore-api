<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\ApiException;
use App\Services\UserService;
use App\Enums\UserRole;

class AuthService
{
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function registerClient(array $data, UserService $userService): array
    {
        $data = array_merge($data, ['role' => UserRole::CLIENT]);
        $user = $userService->store($data);

        $token = $user->createToken('UserToken')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function registerMod(array $data, UserService $userService): array
    {
        $data = array_merge($data, ['role' => UserRole::CLIENT]);
        $user = $userService->store($data);

        $token = $user->createToken('UserToken')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * @param array<string, mixed> $credentials
     * @return array<string, mixed>
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new ApiException('Invalid credentials provided.', null, 401);
        }

        $token = $user->createToken('UserToken')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function validatePassword(string $user_password, string $password): void
    {
        if (! Hash::check($password, $user_password)) {
            throw new ApiException('The current password is incorrect.', null, 403);
        }
    }
}
