<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CartPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Cart $cart): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Cart $cart): bool
    {
        return false;
    }

    public function forceDelete(User $user, Cart $cart): bool
    {
        return false;
    }

    // public function delete(User $user, Cart $cart): bool
    // {
    //     return false;
    // }

    // public function restore(User $user, Cart $cart): bool
    // {
    //     return false;
    // }
}
