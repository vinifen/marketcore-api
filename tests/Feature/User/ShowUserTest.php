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

    public function test_should_fail_when_user_tries_to_access_other_user(): void
    {
        $user1 = $this->createTestUser();
        $user2 = $this->createTestUser([
            'email' => 'another@example.com',
        ]);

        $response = $this->actingAs($user1)->getJson("api/users/{$user2->id}");

        $response->assertStatus(403)
                ->assertJson($this->defaultErrorResponse('You are not authorized to show this resource.'));
    }
}
