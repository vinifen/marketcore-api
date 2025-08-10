<?php

namespace Tests\Feature\User;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_update_user_name_email_and_password(): void
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->putJson("api/users/{$user->id}", [
            'name' => $this->newName,
            'email' => $this->newEmail,
            'new_password' => $this->newPassword,
            'new_password_confirmation' => $this->newPassword,
            'current_password' => $this->originalPassword,
        ]);

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'name' => $this->newName,
                    'email' => $this->newEmail,
                ]));

        $user->refresh();
        $this->assertEquals($this->newName, $user->name);
        $this->assertEquals($this->newEmail, $user->email);
    }

    public function test_should_update_user_only_name(): void
    {
        $user = $this->createTestUser();
        $response = $this->actingAs($user)->putJson("api/users/{$user->id}", [
            'name' => $this->newName,
        ]);

        $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'data' => [
                'name' => $this->newName,
                'email' => $this->originalEmail,
            ],
        ]);

        $user->refresh();
        $this->assertEquals($this->newName, $user->name);
    }

    public function test_should_fail_update_user_not_authenticated(): void
    {
        $user = $this->createTestUser();

        $response = $this->putJson("api/users/{$user->id}", [
            'name' => $this->newName,
            'email' => $this->newEmail,
            'current_password' => $this->originalPassword,
        ]);

        $response->assertStatus(401);
    }

    public function test_should_fail_update_user_password_with_incorrect_data(): void
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->putJson("api/users/{$user->id}", [
            'name' => '1',
            'email' => 'sdasd',
            'password' => 'as',
        ]);

        $response->assertStatus(422)
            ->assertJson($this->defaultErrorResponse('User update request failed due to invalid data.', [
                'name' => ['The name field must be at least 2 characters.'],
                'email' => ['The email field must be a valid email address.'],
                'current_password' => ['The current password field is required.'],
            ]));

        $this->assertEquals($this->originalName, $user->name);
        $this->assertEquals($this->originalEmail, $user->email);
    }

    public function test_should_fail_when_current_password_is_wrong(): void
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->putJson("api/users/{$user->id}", [
            'name' => $this->newName,
            'email' => $this->newEmail,
            'current_password' => 'wrong-password',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'errors' => [
                    'message' => 'The current password is incorrect.',
                ],
            ]);

        $user->refresh();
        $this->assertEquals($this->originalName, $user->name);
        $this->assertEquals($this->originalEmail, $user->email);
    }

    public function test_should_fail_when_email_already_exists(): void
    {
        $user = $this->createTestUser();
        $anotherUser = User::factory()->create(['email' => 'already@taken.com']);

        $response = $this->actingAs($user)->putJson("api/users/{$user->id}", [
            'name' => $this->newName,
            'email' => $anotherUser->email,
            'current_password' => $this->originalPassword,
        ]);

        $response->assertStatus(422)
                ->assertJson($this->defaultErrorResponse('User update request failed due to invalid data.', [
                    'email' => ['The email has already been taken.'],
                ]));

        $user->refresh();
        $this->assertNotEquals($anotherUser->email, $user->email);
        $this->assertEquals($this->originalEmail, $user->email);
    }

    public function test_should_fail_when_client_update_modearator(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        $moderator = $this->createTestUser(['email' => 'moderator@email.com', 'role' => UserRole::MODERATOR]);

        $response = $this->actingAs($client)->putJson("api/users/{$moderator->id}", [
            'name' => $this->newName,
        ]);

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to update this resource.'));
    }

    public function test_should_update_client_with_admin(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $client = $this->createTestUser(['email' => 'client@email.com', 'role' => UserRole::CLIENT]);

        $response = $this->actingAs($admin)->putJson("api/users/{$client->id}", [
            'name' => $this->newName,
        ]);

        $response->assertStatus(200)
        ->assertJson($this->defaultSuccessResponse([
            'name' => $this->newName,
            'email' => "client@email.com",
        ]));

        $client->refresh();
        $this->assertEquals($this->newName, $client->name);
    }

    public function test_should_fail_when_moderator_update_client(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $client = $this->createTestUser(['email' => 'client@email.com', 'role' => UserRole::CLIENT]);

        $response = $this->actingAs($moderator)->putJson("api/users/{$client->id}", [
            'name' => $this->newName,
        ]);

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to update this resource.'));
    }

    public function test_should_fail_when_moderator_update_moderator(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $moderator2 = $this->createTestUser(['email' => 'moderator2@email.com', 'role' => UserRole::MODERATOR]);

        $response = $this->actingAs($moderator)->putJson("api/users/{$moderator2->id}", [
            'name' => $this->newName,
        ]);

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to update this resource.'));
    }
}
