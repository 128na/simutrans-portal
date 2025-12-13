<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\TestCase;

class GetTopPageArticlesTest extends TestCase
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
        // アナウンス記事を3件作成
        for ($i = 0; $i < 3; $i++) {
            $this->createAnnounce($this->user);
        }

        // pak128-japan記事を5件作成
        for ($i = 0; $i < 5; $i++) {
            $article = $this->createAddonIntroduction($this->user);
            $category = Category::where('type', CategoryType::Pak)->where('slug', '128-japan')->firstOrFail();
            $article->categories()->save($category);
        }

        // pak128記事を5件作成
        for ($i = 0; $i < 5; $i++) {
            $article = $this->createAddonIntroduction($this->user);
            $category = Category::where('type', CategoryType::Pak)->where('slug', '128')->firstOrFail();
            $article->categories()->save($category);
        }

        // pak64記事を5件作成
        for ($i = 0; $i < 5; $i++) {
            $article = $this->createAddonIntroduction($this->user);
            $category = Category::where('type', CategoryType::Pak)->where('slug', '64')->firstOrFail();
            $article->categories()->save($category);
        }

        // 一般記事を5件作成（announce以外）
        for ($i = 0; $i < 5; $i++) {
            $this->createPage($this->user);
        }

        $result = $this->articleRepository->getTopPageArticles();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('announces', $result);
        $this->assertArrayHasKey('pak128Japan', $result);
        $this->assertArrayHasKey('pak128', $result);
        $this->assertArrayHasKey('pak64', $result);
        $this->assertArrayHasKey('pages', $result);

        $this->assertInstanceOf(Collection::class, $result['announces']);
        $this->assertInstanceOf(Collection::class, $result['pak128Japan']);
        $this->assertInstanceOf(Collection::class, $result['pak128']);
        $this->assertInstanceOf(Collection::class, $result['pak64']);
        $this->assertInstanceOf(Collection::class, $result['pages']);

        $this->assertCount(3, $result['announces'], 'アナウンス記事が3件取得できること');
        $this->assertCount(5, $result['pak128Japan'], 'pak128-japan記事が5件取得できること');
        $this->assertCount(5, $result['pak128'], 'pak128記事が5件取得できること');
        $this->assertCount(5, $result['pak64'], 'pak64記事が5件取得できること');
        $this->assertCount(5, $result['pages'], '一般記事が5件取得できること');
    }

    public function testカスタム件数(): void
    {
        // アナウンス記事を10件作成（3件より多く作成して制限をテスト）
        for ($i = 0; $i < 10; $i++) {
            $this->createAnnounce($this->user);
        }

        // pak128-japan記事を10件作成
        for ($i = 0; $i < 10; $i++) {
            $article = $this->createAddonIntroduction($this->user);
            $category = Category::where('type', CategoryType::Pak)->where('slug', '128-japan')->firstOrFail();
            $article->categories()->save($category);
        }

        // カスタム件数でテスト
        $result = $this->articleRepository->getTopPageArticles(
            announcesLimit: 2,
            pak128JapanLimit: 3
        );

        $this->assertCount(2, $result['announces'], 'アナウンス記事が指定した2件取得できること');
        $this->assertCount(3, $result['pak128Japan'], 'pak128-japan記事が指定した3件取得できること');
    }

    public function test削除済みユーザーの記事は取得されない(): void
    {
        $deletedUser = User::factory()->create(['deleted_at' => now()]);

        // 削除済みユーザーの記事を作成
        $article = Article::factory()->page()->publish()->create(['user_id' => $deletedUser->id]);
        $category = Category::where('type', CategoryType::Page)->where('slug', 'announce')->firstOrFail();
        $article->categories()->save($category);

        // 通常ユーザーの記事を作成
        $this->createAnnounce($this->user);

        $result = $this->articleRepository->getTopPageArticles();

        $this->assertCount(1, $result['announces'], '削除済みユーザーの記事は除外されること');
    }

    public function test非公開記事は取得されない(): void
    {
        // 非公開のアナウンス記事を作成
        $article = Article::factory()->page()->draft()->create(['user_id' => $this->user->id]);
        $category = Category::where('type', CategoryType::Page)->where('slug', 'announce')->firstOrFail();
        $article->categories()->save($category);

        // 公開のアナウンス記事を作成
        $this->createAnnounce($this->user);

        $result = $this->articleRepository->getTopPageArticles();

        $this->assertCount(1, $result['announces'], '非公開記事は除外されること');
    }

    public function test論理削除された記事は取得されない(): void
    {
        // 削除されたアナウンス記事を作成
        $article = Article::factory()->page()->publish()->deleted()->create(['user_id' => $this->user->id]);
        $category = Category::where('type', CategoryType::Page)->where('slug', 'announce')->firstOrFail();
        $article->categories()->save($category);

        // 通常のアナウンス記事を作成
        $this->createAnnounce($this->user);

        $result = $this->articleRepository->getTopPageArticles();

        $this->assertCount(1, $result['announces'], '論理削除された記事は除外されること');
    }

    public function testarticle_typeが正しく設定される(): void
    {
        $this->createAnnounce($this->user);

        $article = $this->createAddonIntroduction($this->user);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '128-japan')->firstOrFail();
        $article->categories()->save($category);

        $result = $this->articleRepository->getTopPageArticles();

        $this->assertNotEmpty($result['announces']);
        $firstAnnounce = $result['announces']->first();
        $this->assertNotNull($firstAnnounce);
        /** @phpstan-ignore-next-line */
        $this->assertEquals('announces', $firstAnnounce->article_type);

        $this->assertNotEmpty($result['pak128Japan']);
        $firstPak128Japan = $result['pak128Japan']->first();
        $this->assertNotNull($firstPak128Japan);
        /** @phpstan-ignore-next-line */
        $this->assertEquals('pak128Japan', $firstPak128Japan->article_type);
    }
}
