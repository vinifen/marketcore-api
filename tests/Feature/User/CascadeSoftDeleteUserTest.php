<?php

namespace Tests\Feature\User;

use App\Enums\UserRole;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CascadeSoftDeleteUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_soft_delete_user_also_soft_deletes_addresses(): void
    {
        $user = $this->createTestUser();
        $address1 = Address::factory()->create(['user_id' => $user->id]);
        $address2 = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        $response->assertStatus(204);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertSoftDeleted('addresses', ['id' => $address1->id]);
        $this->assertSoftDeleted('addresses', ['id' => $address2->id]);
    }

    public function test_restore_user_also_restores_addresses(): void
    {
        $admin = $this->createTestUser(['email' => 'admin@email.com', 'role' => UserRole::ADMIN]);
        $user = $this->createTestUser();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->deleteJson("api/users/{$user->id}", [
            'password' => $this->originalPassword,
        ]);

        // Restore user
        $response = $this->actingAs($admin)->postJson("api/users/{$user->id}/restore");
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('addresses', ['id' => $address->id, 'deleted_at' => null]);
    }
}