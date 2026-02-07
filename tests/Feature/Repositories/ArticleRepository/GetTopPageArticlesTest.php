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

        $result = $this->articleRepository->getTopPageArticles();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('announces', $result);

        $this->assertInstanceOf(Collection::class, $result['announces']);

        $this->assertCount(3, $result['announces'], 'アナウンス記事が3件取得できること');
    }

    public function testカスタム件数(): void
    {
        // アナウンス記事を10件作成（3件より多く作成して制限をテスト）
        for ($i = 0; $i < 10; $i++) {
            $this->createAnnounce($this->user);
        }

        // カスタム件数でテスト
        $result = $this->articleRepository->getTopPageArticles(announcesLimit: 2);

        $this->assertCount(2, $result['announces'], 'アナウンス記事が指定した2件取得できること');
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

        $result = $this->articleRepository->getTopPageArticles();

        $this->assertNotEmpty($result['announces']);
        $firstAnnounce = $result['announces']->first();
        $this->assertNotNull($firstAnnounce);
        /** @phpstan-ignore-next-line */
        $this->assertEquals('announces', $firstAnnounce->article_type);
    }
}
