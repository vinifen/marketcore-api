<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Policies\Concerns\AuthorizesActions;
use App\Enums\OrderStatus;

class OrderPolicy
{
    use AuthorizesActions;

    public function viewAny(User $authUser): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isStaff(),
            null,
            'You do not have permission to view any orders.'
        );
        return true;
    }

    public function view(User $authUser, Order $order): true
    {
        $this->authorizeUnlessPrivileged(
            $authUser->id === $order->user_id,
            $authUser->isStaff(),
            'view'
        );
        return true;
    }

    public function create(User $authUser): true
    {
        $requestedUserId = request()->input('user_id');
        $this->authorizeUnlessPrivileged(
            $authUser->id === $requestedUserId,
            $authUser->isAdmin(),
            'create'
        );
        return true;
    }

    public function update(User $authUser, Order $order): true
    {
        $this->authorizeUnlessPrivileged(
            $order->user_id === $authUser->id &&
            $order->status !== OrderStatus::CANCELED &&
            $order->status !== OrderStatus::COMPLETED &&
            $order->status !== OrderStatus::SHIPPED,
            $authUser->isAdmin(),
            'update'
        );
        return true;
    }

    public function updateStatus(User $authUser, Order $order): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'updateStatus'
        );
        return true;
    }

    public function cancel(User $authUser, Order $order): true
    {
        $this->authorizeUnlessPrivileged(
            $order->user_id === $authUser->id &&
            $order->status !== OrderStatus::CANCELED &&
            $order->status !== OrderStatus::COMPLETED &&
            $order->status !== OrderStatus::SHIPPED,
            $authUser->isAdmin(),
            'cancel'
        );
        return true;
    }

    public function delete(User $authUser, Order $order): true
    {
        $this->authorizeUnlessPrivileged(
            $order->user_id === $authUser->id,
            $authUser->isAdmin(),
            'delete'
        );
        return true;
    }

    public function restore(User $authUser, Order $order): true
    {
        $this->authorizeUnlessPrivileged(
            $order->user_id === $authUser->id,
            $authUser->isAdmin(),
            'restore'
        );
        return true;
    }

    public function forceDelete(User $authUser, Order $order): true
    {
        $this->authorizeUnlessPrivileged(
            $order->user_id === $authUser->id,
            $authUser->isAdmin(),
            'delete'
        );
        return true;
    }
}
