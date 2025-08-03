<?php

namespace Tests\Feature\Coupon;

use App\Enums\UserRole;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_coupon(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);

        $payload = [
            'code' => 'TESTCOUPON',
            'end_date' => now()->addDays(10)->toDateString(),
            'discount_percentage' => 10.5,
        ];

        $response = $this->actingAs($admin)->postJson('/api/coupons', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'code' => 'TESTCOUPON',
                'discount_percentage' => 10.5,
            ]);

        $this->assertDatabaseHas('coupons', [
            'code' => 'TESTCOUPON',
        ]);
    }

    public function test_non_admin_cannot_create_coupon(): void
    {
        $user = $this->createTestUser(['role' => UserRole::MODERATOR]);

        $payload = [
            'code' => 'NOADMIN',
            'end_date' => now()->addDays(5)->toDateString(),
            'discount_percentage' => 5,
        ];

        $response = $this->actingAs($user)->postJson('/api/coupons', $payload);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_coupon(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $coupon = Coupon::factory()->create(['code' => 'UPDATEME']);

        $payload = [
            'discount_percentage' => 25,
        ];

        $response = $this->actingAs($admin)->putJson("/api/coupons/{$coupon->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'discount_percentage' => 25,
            ]);

        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'discount_percentage' => 25,
        ]);
    }

    public function test_non_admin_cannot_update_coupon(): void
    {
        $user = $this->createTestUser(['role' => UserRole::CLIENT]);
        $coupon = Coupon::factory()->create();

        $payload = [
            'discount_percentage' => 50,
        ];

        $response = $this->actingAs($user)->putJson("/api/coupons/{$coupon->id}", $payload);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_coupon(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);
        $coupon = Coupon::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/coupons/{$coupon->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('coupons', [
            'id' => $coupon->id,
        ]);
    }

    public function test_non_admin_cannot_delete_coupon(): void
    {
        $user = $this->createTestUser(['role' => UserRole::CLIENT]);
        $coupon = Coupon::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/coupons/{$coupon->id}");

        $response->assertStatus(403);
    }

    public function test_staff_can_list_coupons(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        Coupon::factory()->create(['code' => 'LIST1']);
        Coupon::factory()->create(['code' => 'LIST2']);

        $response = $this->actingAs($moderator)->getJson('/api/coupons');

        $response->assertStatus(200)
            ->assertJsonFragment(['code' => 'LIST1'])
            ->assertJsonFragment(['code' => 'LIST2']);
    }

    public function test_client_cannot_list_coupons(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        Coupon::factory()->create();

        $response = $this->actingAs($client)->getJson('/api/coupons');

        $response->assertStatus(403);
    }

    public function test_staff_can_view_coupon(): void
    {
        $moderator = $this->createTestUser(['role' => UserRole::MODERATOR]);
        $coupon = Coupon::factory()->create(['code' => 'VIEWME']);

        $response = $this->actingAs($moderator)->getJson("/api/coupons/{$coupon->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['code' => 'VIEWME']);
    }

    public function test_client_cannot_view_coupon(): void
    {
        $client = $this->createTestUser(['role' => UserRole::CLIENT]);
        $coupon = Coupon::factory()->create();

        $response = $this->actingAs($client)->getJson("/api/coupons/{$coupon->id}");

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_coupons(): void
    {
        $coupon = Coupon::factory()->create();

        $response = $this->getJson("/api/coupons/{$coupon->id}");
        $response->assertStatus(401);

        $response = $this->getJson('/api/coupons');
        $response->assertStatus(401);

        $response = $this->postJson('/api/coupons', []);
        $response->assertStatus(401);

        $response = $this->putJson("/api/coupons/{$coupon->id}", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/coupons/{$coupon->id}");
        $response->assertStatus(401);
    }

    public function test_admin_can_create_coupon_with_start_date(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);

        $payload = [
            'code' => 'DATECOUPON',
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'discount_percentage' => 15.0,
        ];

        $response = $this->actingAs($admin)->postJson('/api/coupons', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.code', 'DATECOUPON')
            ->assertJsonPath('data.discount_percentage', 15);

        $expectedStart = now()->addDays(2)->toDateString();
        $expectedEnd = now()->addDays(10)->toDateString();

        $this->assertNotEmpty($expectedStart);
        $this->assertMatchesRegularExpression('/^' . preg_quote($expectedStart, '/') . '/', $response->json('data.start_date'));

        $this->assertNotEmpty($expectedEnd);
        $this->assertMatchesRegularExpression('/^' . preg_quote($expectedEnd, '/') . '/', $response->json('data.end_date'));

        $this->assertDatabaseHas('coupons', [
            'code' => 'DATECOUPON',
            'start_date' => $expectedStart . ' 00:00:00',
        ]);
    }

    public function test_admin_can_create_coupon_without_start_date(): void
    {
        $admin = $this->createTestUser(['role' => UserRole::ADMIN]);

        $payload = [
            'code' => 'NOSTARTDATE',
            'end_date' => now()->addDays(5)->toDateString(),
            'discount_percentage' => 20.0,
        ];

        $response = $this->actingAs($admin)->postJson('/api/coupons', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.code', 'NOSTARTDATE')
            ->assertJsonPath('data.discount_percentage', 20);

        $expectedStart = now()->toDateString();

        $this->assertNotEmpty($expectedStart);
        $this->assertMatchesRegularExpression('/^' . preg_quote($expectedStart, '/') . '/', $response->json('data.start_date'));

        $this->assertDatabaseHas('coupons', [
            'code' => 'NOSTARTDATE',
            'start_date' => $expectedStart . ' 00:00:00',
        ]);
    }
}
