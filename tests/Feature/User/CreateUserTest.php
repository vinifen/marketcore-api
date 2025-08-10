<?php

namespace Tests\Feature\User;

use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_create_an_user(): void
    {
        $admin = $this->createTestUser(['email' => 'admin@email.com', 'role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->postJson('api/users', [
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => $this->originalPassword,
            'password_confirmation' => $this->originalPassword,
        ]);

        $response->assertStatus(201)
                ->assertJson($this->defaultSuccessResponse([
                    'name' => $this->originalName,
                    'email' => $this->originalEmail,
                ]));

        $this->assertDatabaseHas('users', [
            'email' => $this->originalEmail,
            'role' => UserRole::CLIENT,
        ]);
    }

    public function test_should_fail_if_not_authenticated_trying_to_register_moderator(): void
    {
        $response = $this->postJson('api/users', [
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => $this->originalPassword,
            'password_confirmation' => $this->originalPassword,
        ]);

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));
    }

    public function test_should_ignore_role_param_and_create_user_as_client(): void
    {
        $admin = $this->createTestUser(['email' => 'admin@email.com', 'role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->postJson('api/users', [
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => $this->originalPassword,
            'password_confirmation' => $this->originalPassword,
            'role' => UserRole::MODERATOR,
        ]);

        $response->assertStatus(201)
                ->assertJson($this->defaultSuccessResponse([
                    'name' => $this->originalName,
                    'email' => $this->originalEmail,
                    'role' => UserRole::CLIENT->value,
                ]));

        $this->assertDatabaseHas('users', [
            'email' => $this->originalEmail,
            'role' => UserRole::CLIENT,
        ]);
    }

    public function test_should_fail_when_creating_user_with_duplicate_email(): void
    {
        $admin = $this->createTestUser(['email' => 'admin@email.com', 'role' => UserRole::ADMIN]);

        $this->createTestUser([
            'email' => $this->originalEmail,
            'role' => UserRole::CLIENT,
        ]);

        $response = $this->actingAs($admin)->postJson('api/users', [
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => $this->originalPassword,
            'password_confirmation' => $this->originalPassword,
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_should_fail_if_moderator_tries_to_create_user(): void
    {
        $moderator = $this->createTestUser([
            'email' => 'moderator@email.com',
            'role' => UserRole::MODERATOR,
        ]);

        $response = $this->actingAs($moderator)->postJson('api/users', [
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => $this->originalPassword,
            'password_confirmation' => $this->originalPassword,
        ]);

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to create this resource.'));
    }
}
