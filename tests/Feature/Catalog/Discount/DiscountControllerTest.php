<?php

namespace Tests\Feature\Catalog\Discount;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
