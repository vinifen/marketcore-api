<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_register_new_user(): void
    {
        $response = $this->postJson('api/register', [
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => $this->originalPassword,
            'password_confirmation' => $this->originalPassword,
        ]);

        $response->assertStatus(201)
                ->assertJson($this->defaultSuccessResponse([
                    'user' => [
                        'name' => $this->originalName,
                        'email' => $this->originalEmail,
                    ],
                    'token' => true,
                ]));
    }

    public function test_should_fail_when_email_already_exists(): void
    {
        $this->postJson('api/register', [
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => $this->originalPassword,
            'password_confirmation' => $this->originalPassword,
        ]);

        $response = $this->postJson('api/register', [
            'name' => 'Another Name',
            'email' => $this->originalEmail,
            'password' => 'anotherpass123',
            'password_confirmation' => 'anotherpass123',
        ]);

        $response->assertStatus(422)
                ->assertJson($this->defaultErrorResponse('Create user request error.', [
                    'email' => ['The email has already been taken.'],
                ]));
    }

    public function test_should_fail_when_name_is_too_short_and_password_confirmation_is_missing(): void
    {
        $response = $this->postJson('api/register', [
            'name' => 'A',
            'email' => 'invalid@example.com',
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(422)
                ->assertJson($this->defaultErrorResponse('Create user request error.', [
                    'password' => ['The password field confirmation does not match.'],
                ]));
    }
}
