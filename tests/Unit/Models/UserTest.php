<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_with_factory(): void
    {
        $user = $this->createTestUser();

        $this->assertDatabaseHas('users', [
            'email' => $this->originalEmail,
            'name' => $this->originalName,
        ]);

        $this->assertNotEquals($this->originalPassword, $user->password);
        $this->assertTrue(Hash::check($this->originalPassword, $user->password));
    }

    public function test_fillable_attributes_can_be_mass_assigned(): void
    {
        $user = $this->createTestUser();

        $this->assertEquals($this->originalName, $user->name);
        $this->assertEquals($this->originalEmail, $user->email);
        $this->assertTrue(Hash::check($this->originalPassword, $user->password));
    }

    public function test_user_role_checks(): void
    {
        $admin = $this->createTestUser([
            'role' => UserRole::ADMIN,
            'email' => 'admin@example.com',
        ]);

        $moderator = $this->createTestUser([
            'role' => UserRole::MODERATOR,
            'email' => 'moderator@example.com',
        ]);

        $client = $this->createTestUser([
            'role' => UserRole::CLIENT,
            'email' => 'client@example.com',
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($moderator->isAdmin());
        $this->assertFalse($client->isAdmin());

        $this->assertFalse($admin->isModerator());
        $this->assertTrue($moderator->isModerator());
        $this->assertFalse($client->isModerator());

        $this->assertTrue($admin->isStaff());
        $this->assertTrue($moderator->isStaff());
        $this->assertFalse($client->isStaff());
    }
}
