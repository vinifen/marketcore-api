<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\Actions\UpdateUserAction;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Hash;
use Mockery;

class UpdateUserActionTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_updates_name_only()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('update')->once()->with(['name' => 'João']);

        $action = new UpdateUserAction();

        $result = $action->execute($user, ['name' => 'João']);

        $this->assertSame($user, $result);
    }
}
