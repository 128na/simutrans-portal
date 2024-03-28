<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Tests\Feature\TestCase;

class ControllOptionControllerTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->admin()->create();
    }

    public function testIndex(): void
    {
        $url = '/api/admin/controll_options';

        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);
        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->user->update(['role' => UserRole::Admin]);
        $this->actingAs($this->user);
        $res = $this->getJson($url);
        $res->assertOk();
    }

    public function testToggle(): void
    {
        $url = sprintf('/api/admin/controll_options/%s/toggle', $this->controllOption->key);

        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);
        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $this->user->update(['role' => UserRole::Admin]);
        $this->actingAs($this->user);
        $res = $this->postJson($url);
        $res->assertOk();
    }

    public function testToggle値の切替(): void
    {
        $url = sprintf('/api/admin/controll_options/%s/toggle', $this->controllOption->key);

        $this->user->update(['role' => UserRole::Admin]);
        $this->actingAs($this->user);

        $this->assertDatabaseHas('controll_options', ['key' => $this->controllOption->key, 'value' => true]);
        $res = $this->postJson($url);
        $res->assertOk();
        $this->assertDatabaseHas('controll_options', ['key' => $this->controllOption->key, 'value' => false]);
        $res = $this->postJson($url);
        $res->assertOk();
        $this->assertDatabaseHas('controll_options', ['key' => $this->controllOption->key, 'value' => true]);
    }
}
