<?php

namespace App\Exceptions;

use Exception;

abstract class ApiException extends Exception
{
    protected array $errors;

    public function __construct(array $errors = [], string $message = "", int $code = 400)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getStatusCode(): int
    {
        return 400;
    }

    public function getExpectionMessage(): string
    {
        return 'An error occurred: ' . $this->message;
    }

    public function toArray(): array
    {
        return $this->errors;
    }
}
