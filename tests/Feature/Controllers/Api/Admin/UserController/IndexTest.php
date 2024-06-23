<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin\UserController;

use App\Enums\UserRole;
use App\Models\User;
use Tests\Feature\TestCase;

final class IndexTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setup();
        $this->user = User::factory()->admin()->create();
    }

    public function test(): void
    {
        $this->actingAs($this->user);
        $url = '/api/admin/users';
        $testResponse = $this->getJson($url);
        $testResponse->assertOk();
        $testResponse->assertJsonFragment(['name' => $this->user->name]);
        $testResponse->assertJsonFragment(['name' => $this->user->name]);
    }

    public function test未ログイン(): void
    {
        $url = '/api/admin/users';
        $testResponse = $this->getJson($url);
        $testResponse->assertUnauthorized();
    }

    public function test管理者以外(): void
    {
        $this->user->update(['role' => UserRole::User]);
        $this->actingAs($this->user);
        $url = '/api/admin/users';
        $testResponse = $this->getJson($url);
        $testResponse->assertUnauthorized();
    }
}
