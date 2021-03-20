<?php

namespace Tests;

use App\Models\User;

abstract class AdminTestCase extends TestCase
{
    /**
     * 管理者ユーザー
     */
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function dataUsers()
    {
        yield '未ログイン' => [null, 401];
        yield '一般ユーザー' => ['user', 401];
        yield '管理者ユーザー' => ['admin', 200];
    }
}
