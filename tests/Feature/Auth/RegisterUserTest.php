<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
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
                ->assertJson($this->defaultErrorResponse('User creation request failed due to invalid data.', [
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
                ->assertJson($this->defaultErrorResponse('User creation request failed due to invalid data.', [
                    'password' => ['The password field confirmation does not match.'],
                ]));
    }

    public function test_should_register_moderator(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->postJson('api/register/mod', [
            'name' => 'Moderator Name',
            'email' => 'moderator@example.com',
            'password' => 'moderator123',
            'password_confirmation' => 'moderator123',
        ]);

        $response->assertStatus(201)
                ->assertJson($this->defaultSuccessResponse([
                    'user' => [
                        'name' => 'Moderator Name',
                        'email' => 'moderator@example.com',
                    ],
                    'token' => true,
                ]));

        $this->assertDatabaseHas('users', [
            'email' => 'moderator@example.com',
            'role' => UserRole::MODERATOR,
        ]);
    }

    public function test_should_fail_if_not_authenticated_trying_to_register_moderator(): void
    {
        $response = $this->postJson('api/register/mod', [
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => $this->originalPassword,
            'password_confirmation' => $this->originalPassword,
        ]);

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));
    }

    public function test_should_fail_if_client_tries_to_register_moderator(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);

        $response = $this->actingAs($client)->postJson('api/register/mod', [
            'name' => 'Moderator Name',
            'email' => 'moderator@example.com',
            'password' => 'moderator123',
            'password_confirmation' => 'moderator123',
        ]);

        $response->assertStatus(403);
    }

    public function test_should_fail_if_moderator_tries_to_register_another_moderator(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);

        $response = $this->actingAs($moderator)->postJson('api/register/mod', [
            'name' => 'Another Mod',
            'email' => 'anothermod@example.com',
            'password' => 'anotherpass',
            'password_confirmation' => 'anotherpass',
        ]);

        $response->assertStatus(403);
    }
}
