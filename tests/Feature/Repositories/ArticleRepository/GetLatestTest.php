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

final class GetLatestTest extends TestCase
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
        // pak128-japan記事を作成
        $article = $this->createAddonIntroduction($this->user);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '128-japan')->firstOrFail();
        $article->categories()->save($category);

        $result = $this->articleRepository->getLatest('128-japan', 10);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($article->id, $firstArticle->id);
        $this->assertNotNull($firstArticle->user_nickname);
    }

    public function test件数制限(): void
    {
        // pak128記事を10件作成
        for ($i = 0; $i < 10; $i++) {
            $article = $this->createAddonIntroduction($this->user);
            $category = Category::where('type', CategoryType::Pak)->where('slug', '128')->firstOrFail();
            $article->categories()->save($category);
        }

        $result = $this->articleRepository->getLatest('128', 5);

        $this->assertCount(5, $result, '指定した件数のみ取得できること');
    }

    public function test異なる_pak(): void
    {
        // pak128記事を作成
        $article128 = $this->createAddonIntroduction($this->user);
        $category128 = Category::where('type', CategoryType::Pak)->where('slug', '128')->first();
        $article128->categories()->save($category128);

        // pak64記事を作成
        $article64 = $this->createAddonIntroduction($this->user);
        $category64 = Category::where('type', CategoryType::Pak)->where('slug', '64')->first();
        $article64->categories()->save($category64);

        $result = $this->articleRepository->getLatest('128', 10);

        $this->assertCount(1, $result, 'pak128のみ取得できること');
        $this->assertEquals($article128->id, $result->first()->id);
    }

    public function test削除済みユーザーの記事は取得されない(): void
    {
        $deletedUser = User::factory()->create(['deleted_at' => now()]);

        // 削除済みユーザーの記事
        $article = Article::factory()->addonIntroduction()->publish()->create(['user_id' => $deletedUser->id]);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '128')->firstOrFail();
        $article->categories()->save($category);

        $result = $this->articleRepository->getLatest('128', 10);

        $this->assertEmpty($result, '削除済みユーザーの記事は除外されること');
    }

    public function test非公開記事は取得されない(): void
    {
        // 非公開記事
        $article = Article::factory()->addonIntroduction()->draft()->create(['user_id' => $this->user->id]);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '128')->firstOrFail();
        $article->categories()->save($category);

        $result = $this->articleRepository->getLatest('128', 10);

        $this->assertEmpty($result, '非公開記事は除外されること');
    }

    public function test論理削除された記事は取得されない(): void
    {
        // 削除された記事
        $article = Article::factory()->addonIntroduction()->publish()->deleted()->create(['user_id' => $this->user->id]);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '128')->firstOrFail();
        $article->categories()->save($category);

        $result = $this->articleRepository->getLatest('128', 10);

        $this->assertEmpty($result, '論理削除された記事は除外されること');
    }

    public function testアドオン投稿タイプのみ取得(): void
    {
        // AddonPost記事を作成
        $addonPostArticle = $this->createAddonPost($this->user);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '128')->firstOrFail();
        $addonPostArticle->categories()->save($category);

        // AddonIntroduction記事を作成
        $addonIntroArticle = $this->createAddonIntroduction($this->user);
        $addonIntroArticle->categories()->save($category);

        // Page記事を作成（これは除外されるべき）
        $pageArticle = Article::factory()->page()->publish()->create(['user_id' => $this->user->id]);
        $pageArticle->categories()->save($category);

        $result = $this->articleRepository->getLatest('128', 10);

        $this->assertCount(2, $result, 'アドオン投稿タイプのみ取得できること');
    }

    public function test最新順にソート(): void
    {
        $category = Category::where('type', CategoryType::Pak)->where('slug', '128')->firstOrFail();

        // 古い記事
        $oldArticle = Article::factory()->addonIntroduction()->publish()->create([
            'user_id' => $this->user->id,
            'modified_at' => now()->subDays(10),
        ]);
        $oldArticle->categories()->save($category);

        // 新しい記事
        $newArticle = Article::factory()->addonIntroduction()->publish()->create([
            'user_id' => $this->user->id,
            'modified_at' => now(),
        ]);
        $newArticle->categories()->save($category);

        $result = $this->articleRepository->getLatest('128', 10);

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($newArticle->id, $firstArticle->id, '最新の記事が最初に取得されること');
    }
}
