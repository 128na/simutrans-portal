<?php

namespace Tests\Feature\Controllers\Api\v2\Admin\ArticleController;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private User $admin;
    private Article $article;

    protected function setUp(): void
    {
        parent::setup();
        $this->admin = User::factory()->admin()->create();
        $this->article = Article::factory()->create();
    }

    public function test()
    {
        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'deleted_at' => null,
        ]);

        $this->actingAs($this->admin);
        $url = route('api.v2.admin.articles.destroy', $this->article->id);
        $res = $this->deleteJson($url);
        $res->assertOk();

        $this->assertDatabaseMissing('articles', [
            'id' => $this->article->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
        ]);
    }

    public function test削除済みなら復活()
    {
        $this->article->delete();
        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
        ]);

        $this->actingAs($this->admin);
        $url = route('api.v2.admin.articles.destroy', $this->article->id);
        $res = $this->deleteJson($url);
        $res->assertOk();

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'deleted_at' => null,
        ]);
    }

    public function test未ログイン()
    {
        $url = route('api.v2.admin.articles.destroy', $this->article->id);
        $res = $this->deleteJson($url);
        $res->assertUnauthorized();
    }

    public function test管理者以外()
    {
        $this->actingAs($this->user);
        $url = route('api.v2.admin.articles.destroy', $this->article->id);
        $res = $this->deleteJson($url);
        $res->assertUnauthorized();
    }
}
