<?php

namespace App\Policies;

use App\Models\Test;
use App\Models\User;
use App\Policies\Concerns\AuthorizesActions;

class TestPolicy
{
    use AuthorizesActions;

    public function show(User $authUser, Test $test): true
    {
        $this->authorizeUnlessPrivileged(
            $authUser->id === $test->user_id,
            $authUser->isStaff(),
            'view'
        );
        return true;
    }

    public function create(User $authUser): true
    {
        return true;
    }

    public function update(User $authUser, Test $test): true
    {
        $this->authorizeUnlessPrivileged(
            $authUser->id === $test->user_id,
            $authUser->isStaff(),
            'update'
        );
        return true;
    }

    public function delete(User $authUser, Test $test): true
    {
        $this->authorizeUnlessPrivileged(
            $authUser->id === $test->user_id,
            $authUser->isAdmin(),
            'delete'
        );
        return true;
    }
}
