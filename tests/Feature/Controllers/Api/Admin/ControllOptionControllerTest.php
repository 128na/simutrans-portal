<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin;

use App\Models\ControllOption;
use Tests\TestCase;

class ControllOptionControllerTest extends TestCase
{
    private ControllOption $controllOption;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controllOption = ControllOption::create(['key' => 'dummy', 'value' => true]);
    }

    public function testIndex()
    {
        $url = '/api/admin/controll_options';

        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);
        $res = $this->getJson($url);
        $res->assertUnauthorized();

        $this->user->update(['role' => 'admin']);
        $this->actingAs($this->user);
        $res = $this->getJson($url);
        $res->assertOk();
    }

    public function testToggle()
    {
        $url = "/api/admin/controll_options/{$this->controllOption->key}/toggle";

        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $this->actingAs($this->user);
        $res = $this->postJson($url);
        $res->assertUnauthorized();

        $this->user->update(['role' => 'admin']);
        $this->actingAs($this->user);
        $res = $this->postJson($url);
        $res->assertOk();
    }

    public function testToggle値の切替()
    {
        $url = "/api/admin/controll_options/{$this->controllOption->key}/toggle";

        $this->user->update(['role' => 'admin']);
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
