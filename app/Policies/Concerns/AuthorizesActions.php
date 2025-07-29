<?php

namespace App\Policies\Concerns;

use App\Exceptions\ApiException;

trait AuthorizesActions
{
    protected function authorizeUnlessPrivileged(
        bool $condition,
        bool $hasPrivilege,
        ?string $action = null,
        ?string $customMessage = null
    ): void {
        if (!$hasPrivilege && !$condition) {
            $actionText = $action ?? 'handle';
            $message = $customMessage ?? "You are not authorized to {$actionText} this resource.";
            throw new ApiException($message, null, 403);
        }
    }

    // protected function authorizeOrFail(
    //     bool $condition,
    //     ?string $action = null,
    //     ?string $customMessage = null
    // ): void {
    //     if (!$condition) {
    //         $actionText = $action ?? 'handle';
    //         $message = $customMessage ?? "You are not authorized to {$actionText} this resource.";
    //         throw new ApiException($message, null, 403);
    //     }
    // }

    // protected function authorizeIfStaffOrFail(
    //     UserRole $role,
    //     ?string $action = null,
    //     ?string $customMessage = null
    // ): void {
    //     $allowedRoles = [
    //         UserRole::MODERATOR,
    //         UserRole::ADMIN,
    //     ];

    //     $this->authorizeOrFail(
    //         in_array($role, $allowedRoles, true),
    //         $action,
    //         $customMessage
    //     );
    // }

    // protected function authorizeIfAdminOrFail(
    //     UserRole $role,
    //     ?string $action = null,
    //     ?string $customMessage = null
    // ): void {
    //     $this->authorizeOrFail(
    //         $role === UserRole::ADMIN,
    //         $action,
    //         $customMessage
    //     );
    // }

    // protected function authorizeUnlessStaffOrFail(
    //     bool $condition,
    //     UserRole $role,
    //     ?string $action = null,
    //     ?string $customMessage = null
    // ): void {
    //     $isStaff = in_array($role, [UserRole::MODERATOR, UserRole::ADMIN], true);

    //     $this->authorizeOrFail(
    //         $condition || $isStaff,
    //         $action,
    //         $customMessage
    //     );
    // }

    // protected function authorizeUnlessAdminOrFail(
    //     bool $condition,
    //     UserRole $role,
    //     ?string $action = null,
    //     ?string $customMessage = null
    // ): void {
    //     $this->authorizeOrFail(
    //         $condition || $role === UserRole::ADMIN,
    //         $action,
    //         $customMessage
    //     );
    // }
}
