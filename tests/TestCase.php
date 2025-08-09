<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Enums\UserRole;

abstract class TestCase extends BaseTestCase
{
    protected string $originalName = 'Old Name';
    protected string $originalEmail = 'user@example.com';
    protected string $originalPassword = 'password123';

    protected string $newName = 'New Name';
    protected string $newEmail = 'new@example.com';
    protected string $newPassword = 'new-password123';

    protected string $wrongPassword = 'wrong-password';

    /**
     * @param array<string, mixed> $override
     */
    protected function createTestUser(array $override = []): User
    {
        return User::factory()->create(array_merge([
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => bcrypt($this->originalPassword),
            'role' => UserRole::CLIENT,
        ], $override));
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultSuccessResponse(mixed $data = null): array
    {
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultErrorResponse(string $message, mixed $errors = null): array
    {
        $errorArray = [];

        if (is_array($errors)) {
            $errorArray = $errors;
        } elseif (!is_null($errors)) {
            $errorArray = ['detail' => $errors];
        }

        return [
            'success' => false,
            'errors' => array_merge(
                ['message' => $message],
                $errorArray
            ),
        ];
    }

    public function tinyPng(): string
    {
        // 1x1 transparent PNG
        $b64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMB/ax3u0QAAAAASUVORK5CYII=';
        $data = base64_decode($b64, true);
        $this->assertIsString($data);
        return $data;
    }
}
