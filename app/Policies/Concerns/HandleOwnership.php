<?php

namespace App\Policies\Concerns;

use App\Models\User;
use App\Exceptions\AuthException;

trait HandleOwnership
{
    protected function checkOwner(int $user_id, int $resource_user_id, ?string $action = null, ?string $customMessage = null): void
    {
        if ($user_id !== $resource_user_id) {
            $actionText = $action !== null ? $action : "handle";
            $defaultMessage = "You are not authorized to {$actionText} this resource.";
            $message = $customMessage ?? $defaultMessage;
            throw new AuthException(
                ['auth' => [$message]],
                'Policy error',
                403
            );
        }
    }
}
