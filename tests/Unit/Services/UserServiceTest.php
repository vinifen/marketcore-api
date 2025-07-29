<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UserService;
use App\Enums\UserRole;
use App\Exceptions\ApiException;
use App\Services\AuthService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private string $name;
    private string $email;
    private string $password;

    protected function setUp(): void
    {
        parent::setUp();

        $this->name = 'Test User';
        $this->email = 'test@example.com';
        $this->password = 'secret123';
    }

    public function test_should_store_user_with_valid_data(): void
    {
        $userService = app(UserService::class);

        $user = $userService->store([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($this->name, $user->name);
        $this->assertEquals($this->email, $user->email);
        $this->assertTrue(Hash::check($this->password, $user->password));
    }

    public function test_should_not_allow_admin_role_creation(): void
    {
        $userService = app(UserService::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Cannot create user with ADMIN role.');
        $this->expectExceptionCode(403);

        $userService->store([
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'hackpass',
            'role' => UserRole::ADMIN,
        ]);
    }

    public function test_should_update_only_user_name(): void
    {
        $user = $this->createTestUser();

        $userService = app(UserService::class);
        $updatedUser = $userService->update($user, [
            'name' => $this->newName,
        ], app(AuthService::class));

        $this->assertEquals($this->newName, $updatedUser->name);
    }

    public function test_should_update_only_user_email(): void
    {
        $user = $this->createTestUser();

        $userService = app(UserService::class);
        $updatedUser = $userService->update($user, [
            'email' => $this->newEmail,
            'current_password' => $this->originalPassword,
        ], app(AuthService::class));

        $this->assertEquals($this->newEmail, $updatedUser->email);
    }

    public function test_should_update_user_email_password_name(): void
    {
        $user = $this->createTestUser();

        $userService = app(UserService::class);
        $updatedUser = $userService->update($user, [
            'name' => $this->newName,
            'email' => $this->newEmail,
            'new_password' => $this->newPassword,
            'current_password' => $this->originalPassword,
        ], app(AuthService::class));

        $this->assertEquals($this->newName, $updatedUser->name);
        $this->assertEquals($this->newEmail, $updatedUser->email);
        $this->assertTrue(Hash::check($this->newPassword, $updatedUser->password));
    }

    public function test_should_throw_exception_when_updating_email_without_current_password(): void
    {
        $user = $this->createTestUser();

        $userService = app(UserService::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('The current password is incorrect.');

        $userService->update($user, [
            'email' => $this->newEmail,
        ], app(AuthService::class));
    }

    public function test_should_throw_exception_when_updating_password_with_wrong_current_password(): void
    {
        $user = $this->createTestUser();

        $userService = app(UserService::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('The current password is incorrect.');

        $userService->update($user, [
            'new_password' => $this->newPassword,
            'current_password' => 'wrong-password',
        ], app(AuthService::class));
    }

    public function test_should_throw_exception_when_updating_email_with_wrong_current_password(): void
    {
        $user = $this->createTestUser();

        $userService = app(UserService::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('The current password is incorrect.');

        $userService->update($user, [
            'email' => $this->newEmail,
            'current_password' => 'wrong-password',
        ], app(AuthService::class));
    }
}
