<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin\ControllOptionController;

use App\Enums\UserRole;
use App\Models\ControllOption;
use App\Models\User;
use Tests\Feature\TestCase;

final class ToggleTest extends TestCase
{
    private User $user;

    private ControllOption $controllOption;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->admin()->create();
        $this->controllOption = ControllOption::query()->inRandomOrder()->first();
    }

    public function test_未ログイン(): void
    {
        $url = sprintf('/api/admin/controll_options/%s/toggle', $this->controllOption->key->value);

        $testResponse = $this->postJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test_管理者(): void
    {

        $url = sprintf('/api/admin/controll_options/%s/toggle', $this->controllOption->key->value);

        $this->actingAs($this->user);
        $testResponse = $this->postJson($url);
        $testResponse->assertOk();
    }

    public function test_管理者以外(): void
    {
        $url = sprintf('/api/admin/controll_options/%s/toggle', $this->controllOption->key->value);

        $this->user->update(['role' => UserRole::User]);
        $this->actingAs($this->user);
        $testResponse = $this->postJson($url);
        $testResponse->assertUnauthorized();
    }

    public function testToggle値の切替(): void
    {
        $url = sprintf('/api/admin/controll_options/%s/toggle', $this->controllOption->key->value);

        $this->actingAs($this->user);

        $res = $this->postJson($url);
        $res->assertOk();
        $this->assertFalse($this->controllOption->refresh()->value, 'true->false');

        $res = $this->postJson($url);
        $res->assertOk();
        $this->assertTrue($this->controllOption->refresh()->value, 'false->true');
    }
}
