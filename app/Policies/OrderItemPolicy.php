<?php

namespace App\Policies;

use App\Models\OrderItem;
use App\Models\User;
use App\Policies\Concerns\AuthorizesActions;

class OrderItemPolicy
{
    use AuthorizesActions;

    public function viewAny(User $authUser): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isStaff(),
            null,
            'You do not have permission to view any order items.'
        );
        return true;
    }

    public function view(User $authUser, OrderItem $orderItem): true
    {
        $order = $orderItem->order;
        $this->authorizeUnlessPrivileged(
            $order && $authUser->id === $order->user_id,
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

    public function update(User $authUser, OrderItem $orderItem): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'update'
        );
        return true;
    }

    public function forceDelete(User $authUser, OrderItem $orderItem): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'force delete'
        );
        return true;
    }

    public function delete(User $authUser, OrderItem $orderItem): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'delete'
        );
        return true;
    }

    public function restore(User $authUser, OrderItem $orderItem): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'restore'
        );
        return true;
    }
}
