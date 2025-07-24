<?php

namespace App\Exceptions;

use Exception;


class ApiException extends Exception
{
    /**
     * @var array<string, array<int, string>>
     */
    protected array $errors;

    /**
     * @param array<string, array<int, string>> $errors
     */
    public function __construct(?string $message = null, ?array $errors = null, int $code = 400)
    {
        parent::__construct($message ?? 'Unexpected API error', $code);
        $this->errors = $errors;
    }

    public function getStatusCode(): int
    {
        return is_int($this->code) ? $this->code : 400;
    }

    public function getExceptionMessage(): string
    {
        $message = is_string($this->message) ? $this->message : 'Unknown error';
        return 'Unexpected error occurred: ' . $message;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function toArray(): array
    {
        return $this->errors;
    }
}
