<?php

namespace Tests\Feature\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_throttle_60_per_minute_allows_requests_within_limit(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $response = $this->getJson('/api/');
            $response->assertStatus(200);
        }

        $response = $this->getJson('/api/');
        $response->assertStatus(200);
    }

    public function test_throttle_60_per_minute_blocks_requests_over_limit(): void
    {
        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/');
        }

        $response = $this->getJson('/api/');
        $response->assertStatus(429);
    }

    public function test_throttle_10_per_minute_auth_endpoints_allows_requests_within_limit(): void
    {
        for ($i = 0; $i < 9; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'invalid@example.com',
                'password' => 'wrongpassword'
            ]);
            $this->assertNotEquals(429, $response->status());
        }

        $response = $this->postJson('/api/login', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword'
        ]);
        $this->assertNotEquals(429, $response->status());
    }

    public function test_throttle_10_per_minute_auth_endpoints_blocks_requests_over_limit(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/login', [
                'email' => 'invalid@example.com',
                'password' => 'wrongpassword'
            ]);
        }

        $response = $this->postJson('/api/login', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword'
        ]);
        $response->assertStatus(429);
    }

    public function test_throttle_10_per_minute_register_endpoint_allows_requests_within_limit(): void
    {
        for ($i = 0; $i < 9; $i++) {
            $response = $this->postJson('/api/register', [
                'name' => 'Test User',
                'email' => "test{$i}@example.com",
                'password' => 'password123',
                'password_confirmation' => 'password123'
            ]);
            $this->assertNotEquals(429, $response->status());
        }

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test10@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $this->assertNotEquals(429, $response->status());
    }

    public function test_throttle_10_per_minute_register_endpoint_blocks_requests_over_limit(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/register', [
                'name' => 'Test User',
                'email' => "test{$i}@example.com",
                'password' => 'password123',
                'password_confirmation' => 'password123'
            ]);
        }

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test11@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $response->assertStatus(429);
    }

    public function test_throttle_120_per_minute_public_catalog_allows_requests_within_limit(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $response = $this->getJson('/api/categories');
            $response->assertStatus(200);
        }

        $response = $this->getJson('/api/categories');
        $response->assertStatus(200);
    }

    public function test_throttle_120_per_minute_public_catalog_blocks_requests_over_limit(): void
    {
        for ($i = 0; $i < 120; $i++) {
            $this->getJson('/api/categories');
        }

        $response = $this->getJson('/api/categories');
        $response->assertStatus(429);
    }

    public function test_throttle_120_per_minute_products_endpoint_allows_requests_within_limit(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $response = $this->getJson('/api/products');
            $response->assertStatus(200);
        }

        $response = $this->getJson('/api/products');
        $response->assertStatus(200);
    }

    public function test_throttle_120_per_minute_products_endpoint_blocks_requests_over_limit(): void
    {
        for ($i = 0; $i < 120; $i++) {
            $this->getJson('/api/products');
        }

        $response = $this->getJson('/api/products');
        $response->assertStatus(429);
    }

    public function test_throttle_120_per_minute_authenticated_endpoints_allows_requests_within_limit(): void
    {
        $user = $this->createTestUser();

        for ($i = 0; $i < 100; $i++) {
            $response = $this->actingAs($user)->getJson('/api/users');
            $response->assertStatus(403);
        }

        $response = $this->actingAs($user)->getJson('/api/users');
        $response->assertStatus(403);
    }

    public function test_throttle_120_per_minute_authenticated_endpoints_blocks_requests_over_limit(): void
    {
        $user = $this->createTestUser();

        for ($i = 0; $i < 120; $i++) {
            $this->actingAs($user)->getJson('/api/users');
        }

        $response = $this->actingAs($user)->getJson('/api/users');
        $response->assertStatus(429);
    }

    public function test_throttle_storage_endpoint_allows_requests_within_limit(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $response = $this->getJson('/api/storage/nonexistent-file.jpg');
            $this->assertEquals(404, $response->status());
        }

        $response = $this->getJson('/api/storage/nonexistent-file.jpg');
        $this->assertEquals(404, $response->status());
    }

    public function test_throttle_storage_endpoint_blocks_requests_over_limit(): void
    {
        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/storage/nonexistent-file.jpg');
        }

        $response = $this->getJson('/api/storage/nonexistent-file.jpg');
        $response->assertStatus(429);
    }

    public function test_different_ip_addresses_have_separate_rate_limits(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/login', [
                'email' => 'invalid@example.com',
                'password' => 'wrongpassword'
            ]);
        }

        $response = $this->postJson('/api/login', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword'
        ]);
        $response->assertStatus(429);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '192.168.1.100'])
            ->postJson('/api/login', [
                'email' => 'invalid@example.com',
                'password' => 'wrongpassword'
            ]);
        $this->assertNotEquals(429, $response->status());
    }

    public function test_authenticated_users_have_separate_rate_limits_from_ip(): void
    {
        $user1 = $this->createTestUser(['email' => 'user1@example.com']);
        $user2 = $this->createTestUser(['email' => 'user2@example.com']);

        for ($i = 0; $i < 120; $i++) {
            $this->actingAs($user1)->getJson('/api/users');
        }

        $response = $this->actingAs($user1)->getJson('/api/users');
        $response->assertStatus(429);

        $response = $this->actingAs($user2)->getJson('/api/users');
        $response->assertStatus(403);
    }

    public function test_throttle_web_root_endpoint_allows_requests_within_limit(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $response = $this->get('/');
            $response->assertStatus(200);
        }

        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_throttle_web_root_endpoint_blocks_requests_over_limit(): void
    {
        for ($i = 0; $i < 60; $i++) {
            $this->get('/');
        }

        $response = $this->get('/');
        $response->assertStatus(429);
    }
}
