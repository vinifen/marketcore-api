<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\User;
use App\Policies\Concerns\AuthorizesActions;

class CartPolicy
{
    use AuthorizesActions;

    public function viewAny(User $authUser): true
    {
        $this->authorizeUnlessPrivileged(false, $authUser->isStaff(), 'view any cart');
        return true;
    }

    public function view(User $authUser, Cart $cart): true
    {
        $this->authorizeUnlessPrivileged(
            $cart->user_id === $authUser->id,
            $authUser->isStaff(),
            'view cart'
        );
        return true;
    }

    // public function create(User $authUser): bool
    // {
    //     return false;
    // }

    // public function update(User $authUser, Cart $cart): bool
    // {
    //     return false;
    // }

    // public function forceDelete(User $authUser, Cart $cart): bool
    // {
    //     return false;
    // }

    // public function delete(User $authUser, Cart $cart): bool
    // {
    //     return false;
    // }

    // public function restore(User $authUser, Cart $cart): bool
    // {
    //     return false;
    // }
}
