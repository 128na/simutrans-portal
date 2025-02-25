<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleAnalyticsType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Collection;
use Tests\Feature\TestCase;

final class FindAllForAnalyticsTest extends TestCase
{
    private User $user;

    private Article $article;

    private ArticleRepository $articleRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
        $this->user = User::factory()->create();
        $this->article = Article::factory()->create(['user_id' => $this->user->id]);
    }

    public function test(): void
    {
        $othersArticle = Article::factory()->create();
        $res = $this->articleRepository->findAllForAnalytics(
            $this->user,
            [$this->article->id, $othersArticle->id],
            ArticleAnalyticsType::Daily,
            ['20000101', '21000101']
        );

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertCount(1, $res, '自身の記事のみ取得できること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $res = $this->articleRepository->findAllForAnalytics(
            $this->user,
            [$this->article->id],
            ArticleAnalyticsType::Daily,
            ['20000101', '21000101']
        );

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertCount(1, $res, '非公開記事も取得できること');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->articleRepository->findAllForAnalytics(
            $this->user,
            [$this->article->id],
            ArticleAnalyticsType::Daily,
            ['20000101', '21000101']
        );

        $this->assertInstanceOf(Collection::class, $res);
        $this->assertEmpty($res, '削除済み記事は取得できないこと');
    }
}
