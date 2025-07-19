<?php

namespace App\Exceptions;

use Exception;

abstract class ApiException extends Exception
{
    protected array $errors;

    public function __construct(array $errors = [], ?string $message = null, int $code = 400)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getStatusCode(): int
    {
        return $this->code;
    }

    public function getExceptionMessage(): string
    {
        return 'Unexpected error occurred: ' . $this->message;
    }

    public function toArray(): array
    {
        return $this->errors;
    }
}
