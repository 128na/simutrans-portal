<?php

namespace Tests\Feature\Controllers\Api\v2\Admin\ArticleController;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private User $admin;
    private Article $article;

    protected function setUp(): void
    {
        parent::setup();
        $this->admin = User::factory()->admin()->create();
        $this->article = Article::factory()->publish()->create();
    }

    public function test()
    {
        $this->actingAs($this->admin);
        $url = route('api.v2.admin.articles.update', $this->article->id);
        $res = $this->putJson($url, ['article' => ['status' => 'draft']]);
        $res->assertOk();

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'status' => 'draft',
        ]);
    }

    public function test未ログイン()
    {
        $url = route('api.v2.admin.articles.update', $this->article->id);
        $res = $this->putJson($url);
        $res->assertUnauthorized();
    }

    public function test管理者以外()
    {
        $this->actingAs($this->user);
        $url = route('api.v2.admin.articles.update', $this->article->id);
        $res = $this->putJson($url);
        $res->assertUnauthorized();
    }
}
