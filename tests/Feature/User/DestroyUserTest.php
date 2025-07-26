<?php

namespace Tests\Feature\User;

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

        $response->assertStatus(200)
                ->assertJson($this->defaultSuccessResponse([
                    'message' => 'User deleted successfully.',
                ]));

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
}
