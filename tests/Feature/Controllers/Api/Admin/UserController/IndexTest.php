<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin\UserController;

use App\Enums\UserRole;
use App\Models\User;
use Tests\Feature\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setup();
        $this->user = User::factory()->admin()->create();
    }

    public function test(): void
    {
        $this->actingAs($this->user);
        $url = '/api/admin/users';
        $res = $this->getJson($url);
        $res->assertOk();
        $res->assertJsonFragment(['name' => $this->user->name]);
        $res->assertJsonFragment(['name' => $this->user->name]);
    }

    public function test未ログイン(): void
    {
        $url = '/api/admin/users';
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }

    public function test管理者以外(): void
    {
        $this->user->update(['role' => UserRole::User]);
        $this->actingAs($this->user);
        $url = '/api/admin/users';
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }
}
