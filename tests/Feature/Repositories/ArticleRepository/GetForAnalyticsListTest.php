<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\TestCase;

class GetForAnalyticsListTest extends TestCase
{
    private ArticleRepository $articleRepository;

    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->articleRepository = app(ArticleRepository::class);
    }

    public function test(): void
    {
        $article = $this->createPage($this->user);

        $result = $this->articleRepository->getForAnalyticsList($this->user);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals($article->id, $result->first()->id);
    }

    public function test自分の記事のみ取得(): void
    {
        $otherUser = User::factory()->create();

        // 自分の記事
        $myArticle = $this->createPage($this->user);

        // 他人の記事
        $this->createPage($otherUser);

        $result = $this->articleRepository->getForAnalyticsList($this->user);

        $this->assertCount(1, $result, '自分の記事のみ取得できること');
        $this->assertEquals($myArticle->id, $result->first()->id);
    }

    public function test非公開記事は取得されない(): void
    {
        // 下書き記事
        Article::factory()->page()->draft()->create(['user_id' => $this->user->id]);

        // 公開記事
        $this->createPage($this->user);

        $result = $this->articleRepository->getForAnalyticsList($this->user);

        $this->assertCount(1, $result, '公開記事のみ取得されること');
    }

    public function test論理削除された記事は取得されない(): void
    {
        Article::factory()->page()->publish()->deleted()->create(['user_id' => $this->user->id]);

        // 通常記事
        $this->createPage($this->user);

        $result = $this->articleRepository->getForAnalyticsList($this->user);

        $this->assertCount(1, $result, '論理削除された記事は除外されること');
    }

    public function test最新順にソート(): void
    {
        // 古い記事
        $oldArticle = Article::factory()->page()->publish()->create([
            'user_id' => $this->user->id,
            'modified_at' => now()->subDays(10),
        ]);

        // 新しい記事
        $newArticle = Article::factory()->page()->publish()->create([
            'user_id' => $this->user->id,
            'modified_at' => now(),
        ]);

        $result = $this->articleRepository->getForAnalyticsList($this->user);

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($newArticle->id, $firstArticle->id, '最新の記事が最初に取得されること');
    }

    public function test必要なカラムのみ取得(): void
    {
        $this->createPage($this->user);

        $result = $this->articleRepository->getForAnalyticsList($this->user);

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertNotNull($firstArticle->id);
        $this->assertNotNull($firstArticle->title);
        $this->assertNotNull($firstArticle->published_at);
        $this->assertNotNull($firstArticle->modified_at);
    }

    public function testカウント関連が読み込まれる(): void
    {
        $this->createPage($this->user);

        $result = $this->articleRepository->getForAnalyticsList($this->user);

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertTrue($firstArticle->relationLoaded('totalConversionCount'));
        $this->assertTrue($firstArticle->relationLoaded('totalViewCount'));
    }
}
