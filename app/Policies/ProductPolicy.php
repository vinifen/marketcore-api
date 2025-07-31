<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Policies\Concerns\AuthorizesActions;

class ProductPolicy
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

    public function view(User $authUser, Product $product): true
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
            $authUser->isStaff(),
            'create',
            'You do not have permission to create a product.'
        );
        return true;
    }

    public function update(User $authUser, Product $product): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isStaff(),
            'update'
        );
        return true;
    }

    public function forceDelete(User $authUser, Product $product): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isStaff(),
            'delete'
        );
        return true;
    }

    // public function delete(User $authUser, Product $product): true
    // {
    //     return true;
    // }

    // public function restore(User $authUser, Product $product): true
    // {
    //     return true;
    // }
}
