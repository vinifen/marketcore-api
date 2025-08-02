<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Policies\Concerns\AuthorizesActions;

class CartItemPolicy
{
    use AuthorizesActions;

    public function viewAny(User $authUser): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isStaff(),
            null,
            'You are not authorized to view any cart items.'
        );
        return true;
    }

    public function view(User $authUser, CartItem $cartItem): true
    {
        $this->authorizeUnlessPrivileged(
            $cartItem->cart && $cartItem->cart->user_id === $authUser->id,
            $authUser->isStaff(),
            'view',
        );
        return true;
    }

    public function create(User $authUser): bool
    {
        $cartId = request()->input('cart_id');
        $cart = Cart::find($cartId);

        $isOwner = $cart instanceof Cart && $cart->user_id === $authUser->id;

        $this->authorizeUnlessPrivileged(
            $isOwner,
            $authUser->isAdmin(),
            'create',
        );
        return true;
    }

    public function update(User $authUser, CartItem $cartItem): true
    {
        $this->authorizeUnlessPrivileged(
            $cartItem->cart && $cartItem->cart->user_id === $authUser->id,
            $authUser->isAdmin(),
            'update',
        );
        return true;
    }

    public function forceDelete(User $authUser, CartItem $cartItem): true
    {
        $this->authorizeUnlessPrivileged(
            $cartItem->cart && $cartItem->cart->user_id === $authUser->id,
            $authUser->isAdmin(),
            'delete',
        );
        return true;
    }

    // public function delete(User $authUser, CartItem $cartItem): bool
    // {
    //     return false;
    // }

    // public function restore(User $authUser, CartItem $cartItem): bool
    // {
    //     return false;
    // }
}
