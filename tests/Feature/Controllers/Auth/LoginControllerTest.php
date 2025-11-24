<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Tests\Feature\TestCase;

final class LoginControllerTest extends TestCase
{
    public function test_show_login_guest(): void
    {
        $testResponse = $this->get(route('login'));

        $testResponse->assertOk();
    }

    public function test_show_login_authenticated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $testResponse = $this->get(route('login'));

        $testResponse->assertRedirect(route('mypage.index'));
    }
}
