<?php

namespace Tests\Unit\Models;

use App\Models\User;
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
}
