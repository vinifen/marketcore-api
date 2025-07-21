<?php

namespace App\Exceptions;

class ValidationException extends ApiException
{
    public function getExceptionMessage(): string
    {
        return is_string ($this->message)
            ? $this->message :
            'Validation error occurred.';
    }

    public function getStatusCode(): int
    {
        return 422;
    }
}
