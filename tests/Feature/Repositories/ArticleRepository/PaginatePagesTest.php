<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Feature\TestCase;

class PaginatePagesTest extends TestCase
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

        $result = $this->articleRepository->paginatePages(24);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(1, $result);
        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($article->id, $firstArticle->id);
    }

    public function test件数制限(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->createPage($this->user);
        }

        $result = $this->articleRepository->paginatePages(10);

        $this->assertCount(10, $result, '指定した件数のみ取得できること');
        $this->assertEquals(30, $result->total(), '全体の件数が正しいこと');
    }

    public function testデフォルト件数制限(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->createPage($this->user);
        }

        $result = $this->articleRepository->paginatePages();

        $this->assertCount(24, $result, 'デフォルトで24件取得できること');
    }

    public function testアナウンス記事は除外される(): void
    {
        // アナウンス記事
        $this->createAnnounce($this->user);

        // 通常の一般記事
        $pageArticle = $this->createPage($this->user);

        $result = $this->articleRepository->paginatePages(24);

        $this->assertCount(1, $result, 'アナウンス記事は除外されること');
        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($pageArticle->id, $firstArticle->id);
    }

    public function test削除済みユーザーの記事は取得されない(): void
    {
        $deletedUser = User::factory()->create(['deleted_at' => now()]);

        Article::factory()->page()->publish()->create(['user_id' => $deletedUser->id]);

        // 通常ユーザーの記事
        $this->createPage($this->user);

        $result = $this->articleRepository->paginatePages(24);

        $this->assertCount(1, $result, '削除済みユーザーの記事は除外されること');
    }

    public function test非公開記事は取得されない(): void
    {
        Article::factory()->page()->draft()->create(['user_id' => $this->user->id]);

        // 公開記事
        $this->createPage($this->user);

        $result = $this->articleRepository->paginatePages(24);

        $this->assertCount(1, $result, '非公開記事は除外されること');
    }

    public function test論理削除された記事は取得されない(): void
    {
        Article::factory()->page()->publish()->deleted()->create(['user_id' => $this->user->id]);

        // 通常記事
        $this->createPage($this->user);

        $result = $this->articleRepository->paginatePages(24);

        $this->assertCount(1, $result, '論理削除された記事は除外されること');
    }

    public function testページ投稿タイプのみ取得(): void
    {
        // Page記事
        $this->createPage($this->user);

        // Markdown記事
        $this->createMarkdown($this->user);

        // AddonIntroduction記事（これは除外されるべき）
        $this->createAddonIntroduction($this->user);

        $result = $this->articleRepository->paginatePages(24);

        $this->assertCount(2, $result, 'PageとMarkdownタイプのみ取得できること');
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

        $result = $this->articleRepository->paginatePages(24);

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($newArticle->id, $firstArticle->id, '最新の記事が最初に取得されること');
    }

    public function test関連データが読み込まれる(): void
    {
        $article = $this->createPage($this->user);
        // createPage()で既にPageカテゴリが設定されているので、別のカテゴリタイプを追加
        $this->attachRandomCategory($article, \App\Enums\CategoryType::Pak);

        $result = $this->articleRepository->paginatePages(24);

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertTrue($firstArticle->relationLoaded('categories'));
        $this->assertTrue($firstArticle->relationLoaded('tags'));
        $this->assertTrue($firstArticle->relationLoaded('attachments'));
        $this->assertTrue($firstArticle->relationLoaded('user'));
    }
}
