<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin\UserController;

use App\Enums\UserRole;
use App\Models\User;
use Tests\Feature\TestCase;

class DestroyTest extends TestCase
{
    private User $user;

    private User $targetUser;

    protected function setUp(): void
    {
        parent::setup();
        $this->user = User::factory()->admin()->create();
        $this->targetUser = User::factory()->admin()->create();
    }

    public function test論理削除済みでなければ論理削除(): void
    {
        $this->actingAs($this->user);

        $url = '/api/admin/users/'.$this->targetUser->id;
        $res = $this->deleteJson($url);
        $res->assertOk();

        $user = User::withTrashed()->findOrFail($this->targetUser->id);
        $this->assertTrue($user->trashed(), '論理削除されている');
    }

    public function test論理削除済みは復活する(): void
    {
        $this->targetUser->delete();

        $this->actingAs($this->user);
        $url = '/api/admin/users/'.$this->targetUser->id;
        $res = $this->deleteJson($url);
        $res->assertOk();

        $user = User::withTrashed()->findOrFail($this->targetUser->id);
        $this->assertFalse($user->trashed(), '論理削除されていない');
    }

    public function test未ログイン(): void
    {
        $url = '/api/admin/users/'.$this->targetUser->id;
        $res = $this->deleteJson($url);
        $res->assertUnauthorized();
    }

    public function test管理者以外(): void
    {
        $this->user->update(['role' => UserRole::User]);
        $this->actingAs($this->user);
        $url = '/api/admin/users/'.$this->targetUser->id;
        $res = $this->deleteJson($url);
        $res->assertUnauthorized();
    }
}
