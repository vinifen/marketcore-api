<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Actions\UpdateUserAction;
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

    public function test_should_update_only_user_name(): void
    {
        $user = User::factory()->create([
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => bcrypt($this->originalPassword),
        ]);

        $updateUserAction = app(UpdateUserAction::class);
        $updatedUser = $updateUserAction->execute($user, [
            'name' => $this->newName,
        ]);

        $this->assertEquals($this->newName, $updatedUser->name);
    }

    public function test_should_update_only_user_email(): void
    {
        $user = User::factory()->create([
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => bcrypt($this->originalPassword),
        ]);

        $updateUserAction = app(UpdateUserAction::class);
        $updatedUser = $updateUserAction->execute($user, [
            'email' => $this->newEmail,
            'current_password' => $this->originalPassword,
        ]);

        $this->assertEquals($this->newEmail, $updatedUser->email);
    }

    public function test_should_update_user_email_password_name(): void
    {
        $user = User::factory()->create([
            'name' => $this->originalName,
            'email' => $this->originalEmail,
            'password' => bcrypt($this->originalPassword),
        ]);

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
}
