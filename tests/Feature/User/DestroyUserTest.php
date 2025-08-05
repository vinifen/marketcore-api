<?php

namespace Tests\Feature\User;

use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_soft_delete_user(): void
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(204);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_should_restore_soft_deleted_user(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN, 'email' => 'admin1@email.com']);
        $user = $this->createTestUser(['email' => 'user1@email.com']);

        $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response = $this->actingAs($admin)->postJson("api/users/{$user->id}/restore");

        $response->assertStatus(200)
            ->assertJson($this->defaultSuccessResponse([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }

    public function test_should_force_delete_user(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN, 'email' => 'admin2@email.com']);
        $user = $this->createTestUser(['email' => 'user2@email.com']);

        $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response = $this->actingAs($admin)->deleteJson("api/users/{$user->id}/force-delete");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_should_fail_delete_user_not_authenticated(): void
    {
        $user = $this->createTestUser(['email' => 'user3@email.com']);

        $response = $this->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));
    }

    public function test_should_not_allow_moderator_to_delete_admin(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR, 'email' => 'moderator1@email.com']);
        $admin = $this->createTestUser(['role' => UserRole::ADMIN, 'email' => 'admin3@email.com']);

        $response = $this->actingAs($moderator)->deleteJson("api/users/{$admin->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_should_allow_admin_to_delete_moderator(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN, 'email' => 'admin4@email.com']);
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR, 'email' => 'moderator2@email.com']);

        $response = $this->actingAs($admin)->deleteJson("api/users/{$moderator->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(204);

        $this->assertSoftDeleted('users', ['id' => $moderator->id]);
    }

    public function test_should_allow_admin_to_delete_client(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN, 'email' => 'admin5@email.com']);
        $client = $this->createTestUser(['role' => UserRole::CLIENT, 'email' => 'client1@email.com']);

        $response = $this->actingAs($admin)->deleteJson("api/users/{$client->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(204);

        $this->assertSoftDeleted('users', ['id' => $client->id]);
    }

    public function test_should_not_allow_client_to_delete_admin_or_moderator(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT, 'email' => 'client2@email.com']);
        $admin = $this->createTestUser(['role' => UserRole::ADMIN, 'email' => 'admin6@email.com']);
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR, 'email' => 'moderator3@email.com']);

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
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR, 'email' => 'moderator4@email.com']);
        $client = $this->createTestUser(['role' => UserRole::CLIENT, 'email' => 'client3@email.com']);

        $response = $this->actingAs($moderator)->deleteJson("api/users/{$client->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));

        $this->assertDatabaseHas('users', ['id' => $client->id]);
    }

    public function test_should_not_restore_if_not_admin(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT, 'email' => 'client4@email.com']);
        $user = $this->createTestUser(['email' => 'user4@email.com']);

        $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response = $this->actingAs($client)->postJson("api/users/{$user->id}/restore");

        $response->assertStatus(403)
            ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));
    }

    public function test_should_not_restore_if_user_not_soft_deleted(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN, 'email' => 'admin7@email.com']);
        $user = $this->createTestUser(['email' => 'user5@email.com']);

        $response = $this->actingAs($admin)->postJson("api/users/{$user->id}/restore");

        $response->assertStatus(404)
            ->assertJson($this->defaultErrorResponse('Trashed model not found.'));
    }

    public function test_should_not_force_delete_if_not_admin(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT, 'email' => 'client5@email.com']);
        $user = $this->createTestUser(['email' => 'user6@email.com']);

        $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response = $this->actingAs($client)->deleteJson("api/users/{$user->id}/force-delete");

        $response->assertStatus(403)
            ->assertJson($this->defaultErrorResponse('You are not authorized to delete this resource.'));
    }

    public function test_should_not_force_delete_if_user_not_soft_deleted(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN, 'email' => 'admin8@email.com']);
        $user = $this->createTestUser(['email' => 'user7@email.com']);

        $response = $this->actingAs($admin)->deleteJson("api/users/{$user->id}/force-delete");

        $response->assertStatus(404)
            ->assertJson($this->defaultErrorResponse('Trashed model not found.'));
    }
}