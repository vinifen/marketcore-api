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
}
