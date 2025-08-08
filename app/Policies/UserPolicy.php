<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\AuthorizesActions;

class UserPolicy
{
    use AuthorizesActions;

    public function viewAny(User $authUser): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isStaff(),
            null,
            "You do not have permission to access this resource."
        );
        return true;
    }

    public function create(User $authUser): true
    {
        $this->authorizeUnlessPrivileged(false, $authUser->isAdmin(), "create");
        return true;
    }

    public function view(User $authUser, User $targetUser): true
    {
        $this->authorizeUnlessPrivileged(
            $authUser->id === $targetUser->id,
            $authUser->isStaff(),
            'show'
        );
        return true;
    }

    public function update(User $authUser, User $targetUser): true
    {
        $this->authorizeUnlessPrivileged(
            $authUser->id === $targetUser->id,
            $authUser->isAdmin(),
            'update'
        );
        return true;
    }

    public function restore(User $authUser, User $targetUser): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'delete'
        );
        return true;
    }

    public function delete(User $authUser, User $targetUser): true
    {
        $this->authorizeUnlessPrivileged(
            $authUser->id === $targetUser->id,
            $authUser->isAdmin(),
            'delete'
        );
        return true;
    }

    public function forceDelete(User $authUser, User $targetUser): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'force delete'
        );
        return true;
    }
}
