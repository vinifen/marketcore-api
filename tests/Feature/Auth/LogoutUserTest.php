<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_logout_user(): void
    {
        $user = $this->createTestUser();

        $loginResponse = $this->postJson('api/login', [
            'email' => $this->originalEmail,
            'password' => $this->originalPassword,
        ]);

        $loginResponse->assertStatus(200);

        $token = $loginResponse->json('data.token');

        $logoutResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('api/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson($this->defaultSuccessResponse([
                'message' => 'Logout successful.',
            ]));

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    public function test_should_fail_logout_user_not_authenticated(): void
    {
        $response = $this->postJson('api/logout');

        $response->assertStatus(401)
                ->assertJson($this->defaultErrorResponse('Unauthenticated.'));
    }
}
