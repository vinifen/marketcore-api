<?php

namespace App\Exceptions;

class AuthException extends ApiException
{
    public function getExceptionMessage(): string
    {
        return is_string($this->message)
            ? $this->message
            : 'Authentication error occurred.';
    }
}