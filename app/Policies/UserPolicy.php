<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\HandleOwnership;

class UserPolicy
{
    use HandleOwnership;

    public function index(User $authUser, User $targetUser): bool
    {
        $this->checkOwner(
            $authUser->id,
            $targetUser->id,
            null,
            "You do not have permission to access this resource."
        );
        return true;
    }

    public function show(User $authUser, User $targetUser): bool
    {
        $this->checkOwner($authUser->id, $targetUser->id, 'show');
        return true;
    }

    public function update(User $authUser, User $targetUser): bool
    {
        $this->checkOwner($authUser->id, $targetUser->id, 'update');
        return true;
    }

    public function delete(User $authUser, User $targetUser): bool
    {
        $this->checkOwner($authUser->id, $targetUser->id, 'delete');
        return true;
    }
}
