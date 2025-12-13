<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\Feature\TestCase;

class GetLatestAllPakTest extends TestCase
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
        $article = $this->createAddonIntroduction($this->user);

        $result = $this->articleRepository->getLatestAllPak(24);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(1, $result);
        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($article->id, $firstArticle->id);
    }

    public function test件数制限(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->createAddonIntroduction($this->user);
        }

        $result = $this->articleRepository->getLatestAllPak(10);

        $this->assertCount(10, $result, '指定した件数のみ取得できること');
        $this->assertEquals(30, $result->total(), '全体の件数が正しいこと');
    }

    public function testデフォルト件数制限(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $this->createAddonIntroduction($this->user);
        }

        $result = $this->articleRepository->getLatestAllPak();

        $this->assertCount(24, $result, 'デフォルトで24件取得できること');
    }

    public function test削除済みユーザーの記事は取得されない(): void
    {
        $deletedUser = User::factory()->create(['deleted_at' => now()]);

        Article::factory()->addonIntroduction()->publish()->create(['user_id' => $deletedUser->id]);

        // 通常ユーザーの記事
        $this->createAddonIntroduction($this->user);

        $result = $this->articleRepository->getLatestAllPak(24);

        $this->assertCount(1, $result, '削除済みユーザーの記事は除外されること');
    }

    public function test非公開記事は取得されない(): void
    {
        Article::factory()->addonIntroduction()->draft()->create(['user_id' => $this->user->id]);

        // 公開記事
        $this->createAddonIntroduction($this->user);

        $result = $this->articleRepository->getLatestAllPak(24);

        $this->assertCount(1, $result, '非公開記事は除外されること');
    }

    public function test論理削除された記事は取得されない(): void
    {
        Article::factory()->addonIntroduction()->publish()->deleted()->create(['user_id' => $this->user->id]);

        // 通常記事
        $this->createAddonIntroduction($this->user);

        $result = $this->articleRepository->getLatestAllPak(24);

        $this->assertCount(1, $result, '論理削除された記事は除外されること');
    }

    public function testアドオン投稿タイプのみ取得(): void
    {
        // AddonPost記事
        $this->createAddonPost($this->user);

        // AddonIntroduction記事
        $this->createAddonIntroduction($this->user);

        // Page記事（これは除外されるべき）
        $this->createPage($this->user);

        $result = $this->articleRepository->getLatestAllPak(24);

        $this->assertCount(2, $result, 'アドオン投稿タイプのみ取得できること');
    }

    public function test最新順にソート(): void
    {
        // 古い記事
        $oldArticle = Article::factory()->addonIntroduction()->publish()->create([
            'user_id' => $this->user->id,
            'modified_at' => now()->subDays(10),
        ]);

        // 新しい記事
        $newArticle = Article::factory()->addonIntroduction()->publish()->create([
            'user_id' => $this->user->id,
            'modified_at' => now(),
        ]);

        $result = $this->articleRepository->getLatestAllPak(24);

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($newArticle->id, $firstArticle->id, '最新の記事が最初に取得されること');
    }

    public function test関連データが読み込まれる(): void
    {
        $article = $this->createAddonIntroduction($this->user);
        $category = Category::where('type', CategoryType::Pak)->inRandomOrder()->first();
        $article->categories()->save($category);

        $result = $this->articleRepository->getLatestAllPak(24);

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertTrue($firstArticle->relationLoaded('categories'));
        $this->assertTrue($firstArticle->relationLoaded('tags'));
        $this->assertTrue($firstArticle->relationLoaded('attachments'));
        $this->assertTrue($firstArticle->relationLoaded('user'));
    }
}
