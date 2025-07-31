<?php

namespace Tests\Feature\User;

use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_delete_user(): void
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_should_fail_delete_user_not_authenticated(): void
    {
        $user = $this->createTestUser();

        $response = $this->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));
    }

    public function test_should_not_allow_moderator_to_delete_admin(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $admin = $this->createTestUser(['email' => 'admin@email.com', 'role' => UserRole::ADMIN]);

        $response = $this->actingAs($moderator)->deleteJson("api/users/{$admin->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_should_allow_admin_to_delete_moderator(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $moderator = $this->createTestUser(['email' => 'moderator@email.com', 'role' => UserRole::MODERATOR]);

        $response = $this->actingAs($admin)->deleteJson("api/users/{$moderator->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', ['id' => $moderator->id]);
    }

    public function test_should_allow_admin_to_delete_client(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $client = $this->createTestUser(['email' => 'client@email.com', 'role' => UserRole::CLIENT]);

        $response = $this->actingAs($admin)->deleteJson("api/users/{$client->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', ['id' => $client->id]);
    }

    public function test_should_not_allow_client_to_delete_admin_or_moderator(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        $admin = $this->createTestUser(['email' => 'admin@email.com', 'role' => UserRole::ADMIN]);
        $moderator = $this->createTestUser(['email' => 'moderator@email.com', 'role' => UserRole::MODERATOR]);

        $responseAdmin = $this->actingAs($client)->deleteJson("api/users/{$admin->id}", [
            'password' => $this->originalPassword,
        ]);

        $responseAdmin->assertStatus(403)
                    ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));

        $responseModerator = $this->actingAs($client)->deleteJson("api/users/{$moderator->id}", [
            'password' => $this->originalPassword,
        ]);

        $responseModerator->assertStatus(403)
                        ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
        $this->assertDatabaseHas('users', ['id' => $moderator->id]);
    }

    public function test_should_not_allow_moderator_to_delete_client(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $client = $this->createTestUser(['email' => 'client@email.com' , 'role' => UserRole::CLIENT]);

        $response = $this->actingAs($moderator)->deleteJson("api/users/{$client->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));

        $this->assertDatabaseHas('users', ['id' => $client->id]);
    }
}
