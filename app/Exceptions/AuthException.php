<?php

namespace App\Exceptions;

class AuthException extends ApiException
{
    public function getExceptionMessage(): string
    {
        return $this->message ?: 'Authentication error occurred.';
    }
}