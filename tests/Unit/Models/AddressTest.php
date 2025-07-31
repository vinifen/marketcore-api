<?php

namespace Tests\Unit\Models;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_can_be_created_with_factory(): void
    {
        $user = User::factory()->create();

        $address = Address::factory()->create([
        'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($user->id, $address->user_id);
    }


    public function test_address_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $address->user);
        $this->assertEquals($user->id, $address->user->id);
    }

    public function test_fillable_attributes(): void
    {
        $address = new Address();

        $this->assertEquals([
            'user_id',
            'street',
            'city',
            'state',
            'postal_code',
            'country',
            'number',
            'complement',
        ], $address->getFillable());
    }
}
