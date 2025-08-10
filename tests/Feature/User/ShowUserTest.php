<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_show_user_details(): void
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->getJson("api/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]));
    }

    public function test_should_fail_show_user_not_authenticated(): void
    {
        $user = $this->createTestUser();

        $response = $this->getJson("api/users/{$user->id}");

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));
    }

    public function test_should_fail_when_user_client_tries_to_access_other_user_client(): void
    {
        $user1 = $this->createTestUser();
        $user2 = $this->createTestUser([
            'email' => 'another@example.com',
        ]);

        $response = $this->actingAs($user1)->getJson("api/users/{$user2->id}");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to show this resource.'));
    }

    public function test_should_allow_moderator_to_view_another_user(): void
    {
        $moderator = $this->createTestUser([
            'email' => 'moderator@example.com',
            'role' => \App\Enums\UserRole::MODERATOR,
        ]);

        $otherUser = $this->createTestUser([
            'email' => 'client@example.com',
        ]);

        $response = $this->actingAs($moderator)->getJson("api/users/{$otherUser->id}");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'email' => $otherUser->email,
                ]));
    }

    public function test_should_allow_admin_to_view_another_user(): void
    {
        $admin = $this->createTestUser([
            'email' => 'admin@example.com',
            'role' => \App\Enums\UserRole::ADMIN,
        ]);

        $otherUser = $this->createTestUser([
            'email' => 'client2@example.com',
        ]);

        $response = $this->actingAs($admin)->getJson("api/users/{$otherUser->id}");

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'email' => $otherUser->email,
                ]));
    }
}
