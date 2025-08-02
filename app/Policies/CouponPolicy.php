<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use App\Policies\Concerns\AuthorizesActions;

class CouponPolicy
{
    use AuthorizesActions;

    public function viewAny(User $authUser): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isStaff(),
            null,
            'You do not have permission to view coupons.'
        );
        return true;
    }

    public function view(User $authUser, Coupon $coupon): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isStaff(),
            'view'
        );
        return true;
    }

    public function create(User $authUser): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'create'
        );
        return true;
    }

    public function update(User $authUser, Coupon $coupon): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'update'
        );
        return true;
    }

    public function forceDelete(User $authUser, Coupon $coupon): true
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