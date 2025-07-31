<?php

namespace Tests\Feature\Address;

use App\Enums\UserRole;
use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_address(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $user = $this->createTestUser(['email' => 'user2@example.com']);

        $payload = [
            'user_id' => $user->id,
            'street' => 'Test Street',
            'number' => '123',
            'complement' => 'Apt 1',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345-678',
            'country' => 'USA',
        ];

        $response = $this->actingAs($admin)->postJson('/api/addresses', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'street' => 'Test Street',
                    'number' => '123',
                    'complement' => 'Apt 1',
                    'city' => 'Test City',
                    'state' => 'Test State',
                    'postal_code' => '12345-678',
                    'country' => 'USA',
                ],
            ]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'street' => 'Test Street',
        ]);
    }

    public function test_moderator_cannot_create_address(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $user = $this->createTestUser(['email' => 'user2@example.com']);

        $payload = [
            'user_id' => $user->id,
            'street' => 'Test Street',
            'number' => '123',
            'complement' => 'Apt 1',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345-678',
            'country' => 'USA',
        ];

        $response = $this->actingAs($moderator)->postJson('/api/addresses', $payload);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_create_address(): void
    {
        $user = $this->createTestUser(['email' => 'user2@example.com']);

        $payload = [
            'user_id' => $user->id,
            'street' => 'Test Street',
            'number' => '123',
            'complement' => 'Apt 1',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345-678',
            'country' => 'USA',
        ];

        $response = $this->postJson('/api/addresses', $payload);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_admin_can_update_address(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $user = $this->createTestUser(['email' => 'user2@example.com']);
        $address = Address::factory()->create(['user_id' => $user->id, 'street' => 'Old Street']);

        $payload = [
            'street' => 'New Street',
        ];

        $response = $this->actingAs($admin)->putJson("/api/addresses/{$address->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'street' => 'New Street',
                ],
            ]);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'street' => 'New Street',
        ]);
    }

    public function test_moderator_cannot_update_address(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $user = $this->createTestUser(['email' => 'user2@example.com']);
        $address = Address::factory()->create(['user_id' => $user->id, 'street' => 'Old Street']);

        $payload = [
            'street' => 'New Street',
        ];

        $response = $this->actingAs($moderator)->putJson("/api/addresses/{$address->id}", $payload);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_address(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $user = $this->createTestUser(['email' => 'user2@example.com']);
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->deleteJson("/api/addresses/{$address->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('addresses', [
            'id' => $address->id,
        ]);
    }

    public function test_moderator_cannot_delete_address(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $user = $this->createTestUser(['email' => 'user2@example.com']);
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($moderator)->deleteJson("/api/addresses/{$address->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_view_address(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $user = $this->createTestUser(['email' => 'user2@example.com']);
        $address = Address::factory()->create(['user_id' => $user->id, 'street' => 'Test Street']);

        $response = $this->actingAs($admin)->getJson("/api/addresses/{$address->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'street' => 'Test Street',
                ],
            ]);
    }

    public function test_moderator_can_view_address(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $user = $this->createTestUser(['email' => 'user2@example.com']);
        $address = Address::factory()->create(['user_id' => $user->id, 'street' => 'Test Street']);

        $response = $this->actingAs($moderator)->getJson("/api/addresses/{$address->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'street' => 'Test Street',
                ],
            ]);
    }

    public function test_admin_can_list_addresses(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $user1 = $this->createTestUser(['email' => 'user2@example.com']);
        $user2 = $this->createTestUser(['email' => 'user3@example.com']);
        Address::factory()->create(['user_id' => $user1->id]);
        Address::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($admin)->getJson('/api/addresses');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [['id', 'street', 'number', 'city', 'state', 'postal_code', 'country']],
            ]);
    }

    public function test_moderator_can_list_addresses(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $user1 = $this->createTestUser(['email' => 'user2@example.com']);
        $user2 = $this->createTestUser(['email' => 'user3@example.com']);
        Address::factory()->create(['user_id' => $user1->id]);
        Address::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($moderator)->getJson('/api/addresses');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data' => [['id', 'street', 'number', 'city', 'state', 'postal_code', 'country']],
            ]);
    }

    public function test_guest_cannot_list_addresses(): void
    {
        $response = $this->getJson('/api/addresses');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_client_cannot_create_address_for_another_user(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT, 'email' => 'client@example.com']);
        $otherUser = $this->createTestUser(['email' => 'other@example.com']);

        $payload = [
            'user_id' => $otherUser->id,
            'street' => 'Another Street',
            'number' => '456',
            'complement' => 'Suite 2',
            'city' => 'Other City',
            'state' => 'Other State',
            'postal_code' => '98765-432',
            'country' => 'USA',
        ];

        $response = $this->actingAs($client)->postJson('/api/addresses', $payload);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_address_for_any_user(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN, 'email' => 'admin@example.com']);
        $otherUser = $this->createTestUser(['email' => 'other@example.com']);

        $payload = [
            'user_id' => $otherUser->id,
            'street' => 'Admin Street',
            'number' => '789',
            'complement' => 'Floor 3',
            'city' => 'Admin City',
            'state' => 'Admin State',
            'postal_code' => '55555-000',
            'country' => 'USA',
        ];

        $response = $this->actingAs($admin)->postJson('/api/addresses', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'street' => 'Admin Street',
                    'number' => '789',
                    'complement' => 'Floor 3',
                    'city' => 'Admin City',
                    'state' => 'Admin State',
                    'postal_code' => '55555-000',
                    'country' => 'USA',
                ],
            ]);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $otherUser->id,
            'street' => 'Admin Street',
        ]);
    }
}
