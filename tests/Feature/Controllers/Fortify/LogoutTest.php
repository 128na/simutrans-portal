<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Fortify;

use App\Models\User;
use Tests\Feature\TestCase;

final class LogoutTest extends TestCase
{
    private string $url = '/auth/logout';

    public function testLogout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->assertAuthenticated();

        $this->postJson($this->url);
        $this->assertGuest();
    }
}
