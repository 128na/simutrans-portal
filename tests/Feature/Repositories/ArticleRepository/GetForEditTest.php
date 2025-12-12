<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\Collection;
use Tests\Feature\TestCase;

final class GetForEditTest extends TestCase
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

        $result = $this->articleRepository->getForEdit();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals($article->id, $result->first()->id);
        $this->assertNotNull($result->first()->user_name);
    }

    public function test指定した記事を除外(): void
    {
        $article1 = $this->createPage($this->user);
        $article2 = $this->createPage($this->user);

        $result = $this->articleRepository->getForEdit($article1);

        $this->assertCount(1, $result);
        $this->assertEquals($article2->id, $result->first()->id, '指定した記事は除外されること');
    }

    public function test削除済みユーザーの記事は取得されない(): void
    {
        $deletedUser = User::factory()->create(['deleted_at' => now()]);

        Article::factory()->page()->publish()->create(['user_id' => $deletedUser->id]);

        // 通常ユーザーの記事
        $this->createPage($this->user);

        $result = $this->articleRepository->getForEdit();

        $this->assertCount(1, $result, '削除済みユーザーの記事は除外されること');
    }

    public function test非公開記事は取得されない(): void
    {
        Article::factory()->page()->draft()->create(['user_id' => $this->user->id]);

        // 公開記事
        $this->createPage($this->user);

        $result = $this->articleRepository->getForEdit();

        $this->assertCount(1, $result, '非公開記事は除外されること');
    }

    public function test論理削除された記事は取得されない(): void
    {
        Article::factory()->page()->publish()->deleted()->create(['user_id' => $this->user->id]);

        // 通常記事
        $this->createPage($this->user);

        $result = $this->articleRepository->getForEdit();

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

        $result = $this->articleRepository->getForEdit();

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertEquals($newArticle->id, $firstArticle->id, '最新の記事が最初に取得されること');
    }

    public function test必要なカラムのみ取得(): void
    {
        $this->createPage($this->user);

        $result = $this->articleRepository->getForEdit();

        $firstArticle = $result->first();
        $this->assertNotNull($firstArticle);
        $this->assertNotNull($firstArticle->id);
        $this->assertNotNull($firstArticle->title);
        $this->assertNotNull($firstArticle->user_id);
        $this->assertNotNull($firstArticle->user_name);
    }
}
