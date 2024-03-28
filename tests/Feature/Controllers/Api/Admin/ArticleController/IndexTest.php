<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Admin\ArticleController;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;
use Tests\Feature\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    private Article $article1;

    private Article $article2;

    private Article $article3;

    private Article $article4;

    protected function setUp(): void
    {
        parent::setup();

        $this->user = User::factory()->admin()->create();
        $this->article1 = Article::factory()->create();
        $this->article2 = Article::factory()->draft()->create();
        $this->article3 = Article::factory()->deleted()->create();
        $this->article4 = Article::factory()->create(['user_id' => User::factory()->deleted()->create()->id]);
    }

    public function test(): void
    {
        $this->actingAs($this->user);
        $url = '/api/admin/articles';
        $res = $this->getJson($url);
        $res->assertOk();
        $res->assertJsonFragment(['title' => $this->article1->title]);
        $res->assertJsonFragment(['title' => $this->article2->title]);
        $res->assertJsonFragment(['title' => $this->article3->title]);
        $res->assertJsonFragment(['title' => $this->article4->title]);
    }

    public function test未ログイン(): void
    {
        $url = '/api/admin/articles';
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }

    public function test管理者以外(): void
    {
        $this->user->update(['role' => UserRole::User]);
        $this->actingAs($this->user);
        $url = '/api/admin/articles';
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }
}
