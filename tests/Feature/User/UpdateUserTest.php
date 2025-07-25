<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    protected string $originalName = 'Old Name';
    protected string $originalEmail = 'user@example.com';
    protected string $originalPassword = 'password123';

    protected string $newName = 'New Name';
    protected string $newEmail = 'new@example.com';
    protected string $newPassword = 'new-password123';
    
    private function createTestUser(): User
    {
        return User::factory()->create([
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => bcrypt($this->originalPassword),
        ]);
    }

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
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'name' => $this->newName,
                        'email' => $this->newEmail,
                    ],
                ]);

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
        ->assertJson([
            'success' => false,
            'errors' => [
                'message' => 'Update request error.',
                'name' => [
                    'The name field must be at least 2 characters.',
                ],
                'email' => [
                    'The email field must be a valid email address.',
                ],
                'current_password' => [
                    'The current password field is required.',
                ],
            ],
        ]);

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
}
