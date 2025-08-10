<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_login_user_with_valid_credentials(): void
    {
        $this->createTestUser();

        $response = $this->postJson('api/login', [
            'email' => $this->originalEmail,
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'user' => [
                        'name' => $this->originalName,
                        'email' => $this->originalEmail,
                    ],
                    'token' => true,
                ]));
    }

    public function test_should_fail_login_with_invalid_credentials(): void
    {
        $this->createTestUser();

        $response = $this->postJson('api/login', [
            'email' => $this->originalEmail,
            'password' => $this->wrongPassword,
        ]);

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Invalid credentials provided.'));
    }

    public function test_should_fail_login_with_invalid_email_and_password_format(): void
    {
        $response = $this->postJson('api/login', [
            'email' => 'invalid-email-format',
            'password' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJson($this->defaultErrorResponse('Login request failed due to invalid credentials.', [
                'email' => ['The email field must be a valid email address.'],
                'password' => ['The password field must be at least 8 characters.'],
            ]));
    }
}
