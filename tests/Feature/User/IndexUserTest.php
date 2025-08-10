<?php

namespace Tests\Feature\User;

use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_return_list_of_users_for_admin(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);

        $this->createTestUser(['email' => 'user1@example.com']);
        $this->createTestUser(['email' => 'user2@example.com']);

        $response = $this->actingAs($admin)->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonStructure($this->defaultListStructure());
    }

    public function test_should_return_list_of_users_for_moderator(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);

        $this->createTestUser(['email' => 'user1@example.com']);
        $this->createTestUser(['email' => 'user2@example.com']);

        $response = $this->actingAs($moderator)->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonStructure($this->defaultListStructure());
    }

    public function test_should_fail_for_client_trying_to_list_users(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);

        $response = $this->actingAs($client)->getJson('/api/users');

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You do not have permission to access this resource.'));
    }

    public function test_should_fail_if_unauthenticated_user_tries_to_list_users(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));
    }

    public function test_admin_should_see_all_user_roles_in_list(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);

        $this->createTestUser(['email' => 'moderator@example.com', 'role' => UserRole::MODERATOR]);
        $this->createTestUser(['email' => 'client@example.com', 'role' => UserRole::CLIENT]);

        $response = $this->actingAs($admin)->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonFragment(['email' => 'moderator@example.com'])
                ->assertJsonFragment(['email' => 'client@example.com']);
    }

    public function test_moderator_should_see_all_user_roles_in_list(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);

        $this->createTestUser(['email' => 'admin@example.com', 'role' => UserRole::ADMIN]);
        $this->createTestUser(['email' => 'client@example.com', 'role' => UserRole::CLIENT]);

        $response = $this->actingAs($moderator)->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonFragment(['email' => 'admin@example.com'])
                ->assertJsonFragment(['email' => 'client@example.com']);
    }

    /**
     * @return array{
     *     data: array{'*': list<string>}
     * }
     */
    private function defaultListStructure(): array
    {
        return [
            'data' => [
                '*' => ['id', 'name', 'email', 'role'],
            ],
        ];
    }
}
