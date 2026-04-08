<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\TestCase;

class GetAnnouncesTest extends TestCase
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
        $article = $this->createAnnounce($this->user);

        $result = $this->articleRepository->getAnnounces(10);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($article->id, $firstArticle->id);
        $this->assertNotNull($firstArticle->user_nickname);
    }

    public function test件数制限(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->createAnnounce($this->user);
        }

        $result = $this->articleRepository->getAnnounces(5);

        $this->assertCount(5, $result, '指定した件数のみ取得できること');
    }

    public function testデフォルト件数制限(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->createAnnounce($this->user);
        }

        $result = $this->articleRepository->getAnnounces();

        $this->assertCount(3, $result, 'デフォルトで3件取得できること');
    }

    public function test削除済みユーザーの記事は取得されない(): void
    {
        $deletedUser = User::factory()->create(['deleted_at' => now()]);

        $article = $this->createAnnounce($deletedUser);

        $result = $this->articleRepository->getAnnounces(10);

        $this->assertEmpty($result, '削除済みユーザーの記事は除外されること');
    }

    public function test非公開記事は取得されない(): void
    {
        $article = Article::factory()->page()->draft()->create(['user_id' => $this->user->id]);
        $announceCategory = \App\Models\Category::where('slug', 'announce')->firstOrFail();
        $article->categories()->save($announceCategory);

        $result = $this->articleRepository->getAnnounces(10);

        $this->assertEmpty($result, '非公開記事は除外されること');
    }

    public function test論理削除された記事は取得されない(): void
    {
        $article = Article::factory()->page()->publish()->deleted()->create(['user_id' => $this->user->id]);
        $announceCategory = \App\Models\Category::where('slug', 'announce')->firstOrFail();
        $article->categories()->save($announceCategory);

        $result = $this->articleRepository->getAnnounces(10);

        $this->assertEmpty($result, '論理削除された記事は除外されること');
    }

    public function testページ投稿タイプのみ取得(): void
    {
        // Page記事を作成
        $this->createAnnounce($this->user);

        // Markdown記事を作成
        $this->createMarkdownAnnounce($this->user);

        // AddonIntroduction記事を作成（これは除外されるべき）
        $addonArticle = $this->createAddonIntroduction($this->user);
        $announceCategory = \App\Models\Category::where('slug', 'announce')->firstOrFail();
        $addonArticle->categories()->save($announceCategory);

        $result = $this->articleRepository->getAnnounces(10);

        $this->assertCount(2, $result, 'PageとMarkdownタイプのみ取得できること');
    }

    public function test最新順にソート(): void
    {
        // 古い記事
        $oldArticle = $this->createAnnounce($this->user);
        $oldArticle->update(['modified_at' => now()->subDays(10)]);

        // 新しい記事
        $newArticle = $this->createAnnounce($this->user);
        $newArticle->update(['modified_at' => now()]);

        $result = $this->articleRepository->getAnnounces(10);

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($newArticle->id, $firstArticle->id, '最新の記事が最初に取得されること');
    }
}
