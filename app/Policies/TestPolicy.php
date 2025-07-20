<?php

namespace App\Policies;

use App\Models\Test;
use App\Models\User;
use App\Policies\Concerns\HandleOwnership;

class TestPolicy
{
    use HandleOwnership;

    public function show(User $user, Test $test): bool
    {
        $this->checkOwner($user->id, $test->user_id, 'view');
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Test $test): bool
    {
        $this->checkOwner($user->id, $test->user_id, 'update');
        return true;
    }

    public function delete(User $user, Test $test): bool
    {
        $this->checkOwner($user->id, $test->user_id, 'delete');
        return true;
    }
}
