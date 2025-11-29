<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use App\Models\User;
use Tests\Feature\TestCase;

final class UserControllerTest extends TestCase
{
    public function test_users(): void
    {
        $testResponse = $this->get(route('users.index'));

        $testResponse->assertOk();
    }

    public function test_user(): void
    {
        $user = User::factory()->create();
        $testResponse = $this->get(route('users.show', ['userIdOrNickname' => $user->id]));
        $testResponse->assertOk();

        $testResponse = $this->get(route('users.show', ['userIdOrNickname' => $user->nickname]));
        $testResponse->assertOk();
    }
}
