<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Services\AuthService;
use App\Exceptions\ApiException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected string $name = 'Test User';
    protected string $email = 'test@example.com';
    protected string $password = 'password123';

    protected string $wrongPassword = 'wrong-password';

    private function createTestUser(): User
    {
        return User::factory()->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
        ]);
    }

    public function test_should_register_user_and_return_token(): void
    {
        $authService = app(AuthService::class);

        $result = $authService->register([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);

        $user = $result['user'];

        $this->assertEquals($this->name, $user->name);
        $this->assertEquals($this->email, $user->email);
        $this->assertTrue(Hash::check($this->password, $user->password));
    }

    public function test_should_login_user_with_correct_credentials(): void
    {
        $this->createTestUser();

        $authService = app(AuthService::class);

        $result = $authService->login([
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);

        $this->assertEquals($this->email, $result['user']->email);
    }

    public function test_should_throw_exception_when_logging_in_with_invalid_email(): void
    {
        $authService = app(AuthService::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid credentials provided.');

        $authService->login([
            'email' => 'invalid@example.com',
            'password' => $this->password,
        ]);
    }

    public function test_should_throw_exception_when_logging_in_with_invalid_password(): void
    {
        $this->createTestUser();

        $authService = app(AuthService::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid credentials provided.');

        $authService->login([
            'email' => $this->email,
            'password' => $this->wrongPassword,
        ]);
    }

    public function test_should_validate_correct_password(): void
    {
        $this->expectNotToPerformAssertions();

        $hashedPassword = bcrypt($this->password);
        $authService = app(AuthService::class);

        $authService->validatePassword($hashedPassword, $this->password);
    }

    public function test_should_throw_exception_when_password_is_incorrect(): void
    {
        $hashedPassword = bcrypt($this->password);
        $authService = app(AuthService::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('The current password is incorrect.');

        $authService->validatePassword($hashedPassword, $this->wrongPassword);
    }
}
