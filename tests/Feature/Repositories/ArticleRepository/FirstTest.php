<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Tests\Feature\TestCase;

class FirstTest extends TestCase
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

    public function testユーザー_i_dとスラッグで検索(): void
    {
        $article = $this->createPage($this->user);

        $result = $this->articleRepository->first((string) $this->user->id, $article->slug);

        $this->assertNotNull($result);
        $this->assertEquals($article->id, $result->id);
    }

    public function testニックネームとスラッグで検索(): void
    {
        $this->user->update(['nickname' => 'testnick']);
        $article = $this->createPage($this->user);

        $result = $this->articleRepository->first('testnick', $article->slug);

        $this->assertNotNull($result);
        $this->assertEquals($article->id, $result->id);
    }

    public function test存在しない記事はnull(): void
    {
        $result = $this->articleRepository->first((string) $this->user->id, 'non-existent-slug');

        $this->assertNull($result);
    }

    public function test削除済みユーザーの記事は取得されない(): void
    {
        $article = $this->createPage($this->user);
        $this->user->update(['deleted_at' => now()]);

        $result = $this->articleRepository->first((string) $this->user->id, $article->slug);

        $this->assertNull($result, '削除済みユーザーの記事は取得されないこと');
    }

    public function test非公開記事は取得されない(): void
    {
        $article = Article::factory()->page()->draft()->create(['user_id' => $this->user->id]);

        $result = $this->articleRepository->first((string) $this->user->id, $article->slug);

        $this->assertNull($result, '非公開記事は取得されないこと');
    }

    public function test論理削除された記事は取得されない(): void
    {
        $article = Article::factory()->page()->publish()->deleted()->create(['user_id' => $this->user->id]);

        $result = $this->articleRepository->first((string) $this->user->id, $article->slug);

        $this->assertNull($result, '論理削除された記事は取得されないこと');
    }

    public function test関連データが読み込まれる(): void
    {
        $article = $this->createPage($this->user);
        // createPage()で既にPageカテゴリが設定されているので、別のカテゴリタイプを追加
        $this->attachRandomCategory($article, \App\Enums\CategoryType::Pak);

        $result = $this->articleRepository->first((string) $this->user->id, $article->slug);

        $this->assertNotNull($result);
        $this->assertTrue($result->relationLoaded('categories'));
        $this->assertTrue($result->relationLoaded('tags'));
        $this->assertTrue($result->relationLoaded('attachments'));
        $this->assertTrue($result->relationLoaded('user'));
        $this->assertTrue($result->relationLoaded('articles'));
        $this->assertTrue($result->relationLoaded('relatedArticles'));
    }

    public function testスラッグは_ur_lエンコードされて検索される(): void
    {
        $article = Article::factory()->page()->publish()->create([
            'user_id' => $this->user->id,
            'slug' => 'test-slug-with-spaces',
        ]);

        // スラッグが自動的にURLエンコードされて検索される
        $result = $this->articleRepository->first((string) $this->user->id, 'test-slug-with-spaces');

        $this->assertNotNull($result);
        $this->assertEquals($article->id, $result->id);
    }
}
