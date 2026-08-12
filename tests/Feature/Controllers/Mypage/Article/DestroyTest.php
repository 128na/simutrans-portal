<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\Article;

use App\Models\Article;
use App\Models\User;
use App\Repositories\Article\MypageArticleRepository;
use Tests\Feature\TestCase;

class DestroyTest extends TestCase
{
    private User $user;

    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->article = Article::factory()->for($this->user)->addonIntroduction()->create();
    }

    public function test_未ログイン(): void
    {
        $url = '/api/v2/articles/'.$this->article->id;

        $testResponse = $this->deleteJson($url);
        $testResponse->assertUnauthorized();

        $this->assertNull($this->article->fresh()?->deleted_at);
    }

    public function test_他人の記事は削除できない(): void
    {
        $otherUser = User::factory()->create();

        $url = '/api/v2/articles/'.$this->article->id;

        $this->actingAs($otherUser);

        $testResponse = $this->deleteJson($url);
        $testResponse->assertForbidden();

        $this->assertNull($this->article->fresh()?->deleted_at);
    }

    public function test_自分の記事を削除できる(): void
    {
        $url = '/api/v2/articles/'.$this->article->id;

        $this->actingAs($this->user);

        $testResponse = $this->deleteJson($url);
        $testResponse->assertOk();
        $testResponse->assertJson(['article_id' => $this->article->id]);

        $this->assertNotNull($this->article->fresh()?->deleted_at);
    }

    public function test_削除後はマイページ一覧から消える(): void
    {
        $url = '/api/v2/articles/'.$this->article->id;

        $this->actingAs($this->user);

        $this->deleteJson($url)->assertOk();

        $articles = app(MypageArticleRepository::class)->getForMypageList($this->user);
        $this->assertFalse($articles->contains('id', $this->article->id));
    }

    public function test_存在しない記事は404(): void
    {
        $url = '/api/v2/articles/999999';

        $this->actingAs($this->user);

        $testResponse = $this->deleteJson($url);
        $testResponse->assertNotFound();
    }
}
