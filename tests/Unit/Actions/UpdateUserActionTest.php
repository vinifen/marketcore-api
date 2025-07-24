<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Actions\UpdateUserAction;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Hash;

class UpdateUserActionTest extends TestCase
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

    public function test_should_update_only_user_name(): void
    {
        $user = $this->createTestUser();

        $updateUserAction = app(UpdateUserAction::class);
        $updatedUser = $updateUserAction->execute($user, [
            'name' => $this->newName,
        ]);

        $this->assertEquals($this->newName, $updatedUser->name);
    }

    public function test_should_update_only_user_email(): void
    {
        $user = $this->createTestUser();

        $updateUserAction = app(UpdateUserAction::class);
        $updatedUser = $updateUserAction->execute($user, [
            'email' => $this->newEmail,
            'current_password' => $this->originalPassword,
        ]);

        $this->assertEquals($this->newEmail, $updatedUser->email);
    }

    public function test_should_update_user_email_password_name(): void
    {
        $user = $this->createTestUser();

        $updateUserAction = app(UpdateUserAction::class);
        $updatedUser = $updateUserAction->execute($user, [
            'name' => $this->newName,
            'email' => $this->newEmail,
            'new_password' => $this->newPassword,
            'current_password' => $this->originalPassword,
        ]);

        $this->assertEquals($this->newName, $updatedUser->name);
        $this->assertEquals($this->newEmail, $updatedUser->email);
        $this->assertTrue(Hash::check($this->newPassword, $updatedUser->password));
    }

    public function test_should_throw_exception_when_updating_email_without_current_password(): void
    {
        $user = $this->createTestUser();

        $updateUserAction = app(UpdateUserAction::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Password is incorrect.');

        $updateUserAction->execute($user, [
            'email' => $this->newEmail,
        ]);
    }

    public function test_should_throw_exception_when_updating_password_with_wrong_current_password(): void
    {
        $user = $this->createTestUser();

        $updateUserAction = app(UpdateUserAction::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Password is incorrect.');

        $updateUserAction->execute($user, [
            'new_password' => $this->newPassword,
            'current_password' => 'wrong-password',
        ]);
    }

    public function test_should_throw_exception_when_updating_email_with_wrong_current_password(): void
    {
        $user = $this->createTestUser();

        $updateUserAction = app(UpdateUserAction::class);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Password is incorrect.');

        $updateUserAction->execute($user, [
            'email' => $this->newEmail,
            'current_password' => 'wrong-password',
        ]);
    }
}
