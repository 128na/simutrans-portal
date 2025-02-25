<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\ArticleRepository;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Attachment\FileInfo;
use App\Repositories\ArticleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Tests\Feature\TestCase;

final class PaginateBySearchTest extends TestCase
{
    private ArticleRepository $articleRepository;

    private Article $article;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = app(ArticleRepository::class);
        $this->article = Article::factory()->publish()->create();
    }

    public function test(): void
    {
        Article::factory()->publish()->create();
        $res = $this->articleRepository->paginateBySearch($this->article->title);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertCount(1, $res, 'マッチする記事のみ取得出来ること');
    }

    public function testコンテンツ(): void
    {
        $this->article->update(['contents' => ['description' => 'find me']]);
        $res = $this->articleRepository->paginateBySearch('find');

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertCount(1, $res, 'コンテンツにマッチする記事が取得出来ること');
    }

    public function test添付ファイル情報(): void
    {
        $attachment = $this->createAttachment($this->article->user);
        $attachment->fileInfo()->save(new FileInfo(['data' => ['find me']]));
        $this->article->attachments()->save($attachment);
        $res = $this->articleRepository->paginateBySearch('find');

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertCount(1, $res, '添付ファイル情報にマッチする記事が取得出来ること');
    }

    public function test公開以外のステータス(): void
    {
        $this->article->update(['status' => ArticleStatus::Draft]);
        $res = $this->articleRepository->paginateBySearch($this->article->title);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEmpty($res, '非公開記事は取得できないこと');
    }

    public function test論理削除(): void
    {
        $this->article->delete();
        $res = $this->articleRepository->paginateBySearch($this->article->title);

        $this->assertInstanceOf(LengthAwarePaginator::class, $res);
        $this->assertEmpty($res, '削除済み記事は取得できないこと');
    }
}
