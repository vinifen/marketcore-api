<?php

namespace App\Services;

use App\Models\User;
use App\Services\AuthService;
use App\Enums\UserRole;
use App\Exceptions\ApiException;

class UserService
{
    /**
     * @param array<string, mixed> $data
     */
    public function store(array $data): User
    {
        $role = $data['role'] ?? UserRole::CLIENT;

        if ($role === UserRole::ADMIN) {
            throw new ApiException('Cannot create user with ADMIN role.', null, 403);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => $role,
        ]);

        return $user;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(User $user, array $data, AuthService $authService): User
    {
        if (isset($data['email']) || isset($data['new_password'])) {
            $authService->validatePassword(
                $user->password,
                $data['current_password'] ?? ''
            );

            if (isset($data['new_password'])) {
                $data['password'] = bcrypt($data['new_password']);
                unset($data['new_password']);
            }
        }

        unset($data['current_password']);

        $user->update($data);

        return $user;
    }
    
}
