<?php

namespace Tests\Unit\Services;

use App\Enums\UserRole;
use Tests\TestCase;
use App\Services\AuthService;
use App\Exceptions\ApiException;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_register_user_and_return_token(): void
    {
        $authService = app(AuthService::class);

        $result = $authService->registerClient([
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => $this->originalPassword,
        ], app(UserService::class));

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);

        $user = $result['user'];

        $this->assertEquals($this->originalName, $user->name);
        $this->assertEquals($this->originalEmail, $user->email);
        $this->assertTrue(Hash::check($this->originalPassword, $user->password));
    }

    public function test_should_login_user_with_correct_credentials(): void
    {
        $this->createTestUser();

        $authService = app(AuthService::class);

        $result = $authService->login([
            'email' => $this->originalEmail,
            'password' => $this->originalPassword,
        ]);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);

        $this->assertEquals($this->originalEmail, $result['user']->email);
    }

    public function test_should_throw_exception_when_logging_in_with_invalid_email(): void
    {
        $authService = app(AuthService::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid credentials provided.');

        $authService->login([
            'email' => 'invalid@example.com',
            'password' => $this->originalPassword,
        ]);
    }

    public function test_should_throw_exception_when_logging_in_with_invalid_password(): void
    {
        $this->createTestUser();

        $authService = app(AuthService::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid credentials provided.');

        $authService->login([
            'email' => $this->originalEmail,
            'password' => $this->wrongPassword,
        ]);
    }

    public function test_should_validate_correct_password(): void
    {
        $this->expectNotToPerformAssertions();

        $hashedPassword = bcrypt($this->originalPassword);
        $authService = app(AuthService::class);

        $authService->validatePassword($hashedPassword, $this->originalPassword);
    }

    public function test_should_throw_exception_when_password_is_incorrect(): void
    {
        $hashedPassword = bcrypt($this->originalPassword);
        $authService = app(AuthService::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('The current password is incorrect.');

        $authService->validatePassword($hashedPassword, $this->wrongPassword);
    }

    public function test_should_register_moderator_and_return_token(): void
    {
        $authService = app(AuthService::class);

        $result = $authService->registerMod([
            'name' => 'Mod User',
            'email' => 'mod@example.com',
            'password' => 'modpassword',
        ], app(UserService::class));

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);

        $user = $result['user'];

        $this->assertEquals('Mod User', $user->name);
        $this->assertEquals('mod@example.com', $user->email);
        $this->assertEquals(UserRole::MODERATOR, $user->role);
        $this->assertTrue(Hash::check('modpassword', $user->password));
    }

}
