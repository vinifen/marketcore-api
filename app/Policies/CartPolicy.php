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
            $authUser->id === $cart->user_id,
            $authUser->isStaff(),
            'view cart'
        );
        return true;
    }

    public function clear(User $authUser, Cart $cart): true
    {
        $this->authorizeUnlessPrivileged(
            $authUser->id === $cart->user_id,
            $authUser->isStaff(),
            null,
            'You are not authorized to clear this cart.'
        );
        return true;
    }
}
