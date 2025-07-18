<?php

namespace App\Exceptions;

class AuthException extends ApiException
{
    public function getStatusCode(): int
    {
        return 422;
    }

    public function getExpectionMessage(): string
    {
        return 'Authentication error occurred.';
    }
}
