<?php

namespace App\Policies;

use App\Models\Discount;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DiscountPolicy
{

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Discount $discount): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Discount $discount): bool
    {
        return false;
    }

    public function forceDelete(User $user, Discount $discount): bool
    {
        return false;
    }

    // public function delete(User $user, Discount $discount): bool
    // {
    //     return false;
    // }

    // public function restore(User $user, Discount $discount): bool
    // {
    //     return false;
    // }
}
