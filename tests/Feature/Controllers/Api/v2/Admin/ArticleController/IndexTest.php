<?php

namespace Tests\Feature\Controllers\Api\v2\Admin\ArticleController;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $admin;
    private Article $article1;
    private Article $article2;
    private Article $article3;
    private Article $article4;

    protected function setUp(): void
    {
        parent::setup();
        $this->admin = User::factory()->admin()->create();
        $this->article1 = Article::factory()->create();
        $this->article2 = Article::factory()->draft()->create();
        $this->article3 = Article::factory()->deleted()->create();
        $this->article4 = Article::factory()->create(['user_id' => User::factory()->deleted()->create()->id]);
    }

    public function test()
    {
        $this->actingAs($this->admin);
        $url = route('api.v2.admin.articles.index');
        $res = $this->getJson($url);
        $res->assertOk();
        $res->assertJsonFragment(['title' => $this->article1->title]);
        $res->assertJsonFragment(['title' => $this->article2->title]);
        $res->assertJsonFragment(['title' => $this->article3->title]);
        $res->assertJsonFragment(['title' => $this->article4->title]);
    }

    public function test未ログイン()
    {
        $url = route('api.v2.admin.articles.index');
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }

    public function test管理者以外()
    {
        $this->actingAs($this->user);
        $url = route('api.v2.admin.articles.index');
        $res = $this->getJson($url);
        $res->assertUnauthorized();
    }
}
