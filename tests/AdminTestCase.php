<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Article;
use App\Models\User;

abstract class AdminTestCase extends TestCase
{
    /**
     * 管理者ユーザー
     */
    protected User $admin;

    protected Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->article = Article::factory()->create();
    }
}
