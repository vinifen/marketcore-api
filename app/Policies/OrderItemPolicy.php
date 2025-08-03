<?php

namespace App\Policies;

use App\Models\OrderItem;
use App\Models\User;

class OrderItemPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, OrderItem $orderItem): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, OrderItem $orderItem): bool
    {
        return false;
    }

    public function forceDelete(User $user, OrderItem $orderItem): bool
    {
        return false;
    }

    // public function delete(User $user, OrderItem $orderItem): bool
    // {
    //     return false;
    // }

    // public function restore(User $user, OrderItem $orderItem): bool
    // {
    //     return false;
    // }
}
