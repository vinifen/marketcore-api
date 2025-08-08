<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;
use App\Policies\Concerns\AuthorizesActions;

class AddressPolicy
{
    use AuthorizesActions;

    public function viewAny(User $authUser): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isStaff(),
            null,
            "You do not have permission to access this resource."
        );
        return true;
    }

    public function view(User $authUser, Address $address): true
    {
        $this->authorizeUnlessPrivileged(
            $authUser->id === $address->user_id,
            $authUser->isStaff(),
            'show'
        );
        return true;
    }

    public function create(User $authUser): true
    {
        $requestedUserId = (int) request()->input('user_id');

        $this->authorizeUnlessPrivileged(
            $authUser->id === $requestedUserId,
            $authUser->isAdmin(),
            null,
            'You do not have permission to create an address for this user.'
        );

        return true;
    }

    public function update(User $authUser, Address $address): true
    {
        $this->authorizeUnlessPrivileged(
            $authUser->id === $address->user_id,
            $authUser->isAdmin(),
            'update'
        );
        return true;
    }

    public function delete(User $authUser, Address $address): true
    {
        $this->authorizeUnlessPrivileged(
            $authUser->id === $address->user_id,
            $authUser->isAdmin(),
            'delete'
        );
        return true;
    }

    public function restore(User $authUser, Address $address): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'restore'
        );
        return true;
    }

    public function forceDelete(User $authUser, Address $address): true
    {
        $this->authorizeUnlessPrivileged(
            false,
            $authUser->isAdmin(),
            'force delete'
        );
        return true;
    }
}
