<?php

namespace App\Actions;

use App\Models\User;
use App\Services\AuthService;

class UpdateUserAction
{
    public function execute(User $user, array $data): User
    {
        if(isset($data['email']) || isset($data['new_password'])) {
            AuthService::validatePassword($user, $data['current_password']);

            if (isset($data['new_password'])) {
                $data['password'] = bcrypt($data['new_password']);
                unset($data['new_password']);
            }
        }

        $user->update($data);
        return $user;
    }
}