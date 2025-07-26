<?php

namespace App\Actions;

use App\Models\User;
use App\Services\AuthService;

class UpdateUserAction
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    /**
     * @param User $user
     * @param array{
     *     name?: string,
     *     email?: string,
     *     new_password?: string,
     *     current_password?: string
     * } $data
     * @return User
     */
    public function execute(User $user, array $data): User
    {
        if (isset($data['email']) || isset($data['new_password'])) {
            $this->authService->validatePassword(
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
