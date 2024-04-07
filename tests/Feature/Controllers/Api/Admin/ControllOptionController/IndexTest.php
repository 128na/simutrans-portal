<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin\ControllOptionController;

use App\Enums\UserRole;
use App\Models\User;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->admin()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/admin/controll_options';

        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }

    public function test_管理者(): void
    {
        $url = '/api/admin/controll_options';

        $this->actingAs($this->user);
        $res = $this->getJson($url);
        $res->assertOk();
    }

    public function test_管理者以外(): void
    {
        $url = '/api/admin/controll_options';

        $this->user->update(['role' => UserRole::User]);
        $this->actingAs($this->user);
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }
}
