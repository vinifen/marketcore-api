<?php

namespace Tests\Feature\Cart;

use Tests\TestCase;

class CartControllerTest extends TestCase
{
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
