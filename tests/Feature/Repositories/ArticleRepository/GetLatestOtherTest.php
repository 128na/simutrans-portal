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

class GetLatestOtherTest extends TestCase
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
        // その他のPAK記事を作成（64, 128, 128-japan以外）
        $article = $this->createAddonIntroduction($this->user);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '256')->firstOrFail();
        $article->categories()->save($category);

        $result = $this->articleRepository->getLatestOther(24);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(1, $result);
        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($article->id, $firstArticle->id);
    }

    public function test除外対象の_pa_kは取得されない(): void
    {
        // pak64記事（除外対象）
        $article64 = $this->createAddonIntroduction($this->user);
        $category64 = Category::where('type', CategoryType::Pak)->where('slug', '64')->first();
        $article64->categories()->save($category64);

        // pak128記事（除外対象）
        $article128 = $this->createAddonIntroduction($this->user);
        $category128 = Category::where('type', CategoryType::Pak)->where('slug', '128')->first();
        $article128->categories()->save($category128);

        // pak128-japan記事（除外対象）
        $article128Japan = $this->createAddonIntroduction($this->user);
        $category128Japan = Category::where('type', CategoryType::Pak)->where('slug', '128-japan')->first();
        $article128Japan->categories()->save($category128Japan);

        // その他のPAK記事
        $otherArticle = $this->createAddonIntroduction($this->user);
        $otherCategory = Category::where('type', CategoryType::Pak)->where('slug', '256')->first();
        $otherArticle->categories()->save($otherCategory);

        $result = $this->articleRepository->getLatestOther(24);

        $this->assertCount(1, $result, '除外対象のPAK記事は取得されないこと');
        $this->assertEquals($otherArticle->id, $result->first()->id);
    }

    public function test件数制限(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $article = $this->createAddonIntroduction($this->user);
            $category = Category::where('type', CategoryType::Pak)->where('slug', '256')->firstOrFail();
            $article->categories()->save($category);
        }

        $result = $this->articleRepository->getLatestOther(10);

        $this->assertCount(10, $result, '指定した件数のみ取得できること');
    }

    public function testデフォルト件数制限(): void
    {
        for ($i = 0; $i < 30; $i++) {
            $article = $this->createAddonIntroduction($this->user);
            $category = Category::where('type', CategoryType::Pak)->where('slug', '256')->firstOrFail();
            $article->categories()->save($category);
        }

        $result = $this->articleRepository->getLatestOther();

        $this->assertCount(24, $result, 'デフォルトで24件取得できること');
    }

    public function test削除済みユーザーの記事は取得されない(): void
    {
        $deletedUser = User::factory()->create(['deleted_at' => now()]);

        $article = Article::factory()->addonIntroduction()->publish()->create(['user_id' => $deletedUser->id]);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '256')->firstOrFail();
        $article->categories()->save($category);

        $result = $this->articleRepository->getLatestOther(24);

        $this->assertEmpty($result, '削除済みユーザーの記事は除外されること');
    }

    public function test非公開記事は取得されない(): void
    {
        $article = Article::factory()->addonIntroduction()->draft()->create(['user_id' => $this->user->id]);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '256')->firstOrFail();
        $article->categories()->save($category);

        $result = $this->articleRepository->getLatestOther(24);

        $this->assertEmpty($result, '非公開記事は除外されること');
    }

    public function test論理削除された記事は取得されない(): void
    {
        $article = Article::factory()->addonIntroduction()->publish()->deleted()->create(['user_id' => $this->user->id]);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '256')->firstOrFail();
        $article->categories()->save($category);

        $result = $this->articleRepository->getLatestOther(24);

        $this->assertEmpty($result, '論理削除された記事は除外されること');
    }

    public function testアドオン投稿タイプのみ取得(): void
    {
        $category = Category::where('type', CategoryType::Pak)->where('slug', '256')->firstOrFail();

        // AddonPost記事
        $addonPostArticle = $this->createAddonPost($this->user);
        $addonPostArticle->categories()->save($category);

        // AddonIntroduction記事
        $addonIntroArticle = $this->createAddonIntroduction($this->user);
        $addonIntroArticle->categories()->save($category);

        // Page記事（これは除外されるべき）
        $pageArticle = $this->createPage($this->user);
        $pageArticle->categories()->save($category);

        $result = $this->articleRepository->getLatestOther(24);

        $this->assertCount(2, $result, 'アドオン投稿タイプのみ取得できること');
    }

    public function test最新順にソート(): void
    {
        $category = Category::where('type', CategoryType::Pak)->where('slug', '256')->firstOrFail();

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

        $result = $this->articleRepository->getLatestOther(24);

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($newArticle->id, $firstArticle->id, '最新の記事が最初に取得されること');
    }

    public function test関連データが読み込まれる(): void
    {
        $article = $this->createAddonIntroduction($this->user);
        $category = Category::where('type', CategoryType::Pak)->where('slug', '256')->firstOrFail();
        $article->categories()->save($category);

        $result = $this->articleRepository->getLatestOther(24);

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertTrue($firstArticle->relationLoaded('categories'));
        $this->assertTrue($firstArticle->relationLoaded('tags'));
        $this->assertTrue($firstArticle->relationLoaded('attachments'));
        $this->assertTrue($firstArticle->relationLoaded('user'));
    }
}
