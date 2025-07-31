<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use App\Policies\Concerns\AuthorizesActions;

class CategoryPolicy
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

    public function view(User $authUser, Category $category): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isStaff(),
            'show'
        );
        return true;
    }

    public function create(User $authUser): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'create',
            'You do not have permission to create a category.'
        );
        return true;
    }

    public function update(User $authUser, Category $category): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'update'
        );
        return true;
    }

    public function forceDelete(User $authUser, Category $category): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'delete'
        );
        return true;
    }

    // public function delete(User $authUser, Category $category): true
    // {
    //     return true;
    // }

    // public function restore(User $authUser, Category $category): true
    // {
    //     return true;
    // }
}
