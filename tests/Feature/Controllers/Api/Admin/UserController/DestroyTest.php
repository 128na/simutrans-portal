<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin\UserController;

use App\Models\User;
use Tests\TestCase;

class DestroyTest extends TestCase
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

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'deleted_at' => null,
        ]);
        $url = '/api/admin/users/'.$this->user->id;
        $res = $this->deleteJson($url);
        $res->assertOk();

        $this->assertDatabaseMissing('users', [
            'id' => $this->user->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
        ]);
    }

    public function test論理削除済みは復活する(): void
    {
        $this->user->delete();
        $this->actingAs($this->user);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
        ]);
        $url = '/api/admin/users/'.$this->user->id;
        $res = $this->deleteJson($url);
        $res->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'deleted_at' => null,
        ]);
    }

    public function test未ログイン(): void
    {
        $url = '/api/admin/users/'.$this->user->id;
        $res = $this->deleteJson($url);
        $res->assertUnauthorized();
    }

    public function test管理者以外(): void
    {
        $this->actingAs($this->user);
        $url = '/api/admin/users/'.$this->user->id;
        $res = $this->deleteJson($url);
        $res->assertUnauthorized();
    }
}
