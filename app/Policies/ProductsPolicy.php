<?php

namespace App\Policies;

use App\Models\Products;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductsPolicy
{
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Products $products): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Products $products): bool
    {
        return false;
    }

    public function forceDelete(User $user, Products $products): bool
    {
        return false;
    }

    // public function delete(User $user, Products $products): bool
    // {
    //     return false;
    // }

    // public function restore(User $user, Products $products): bool
    // {
    //     return false;
    // }
}

