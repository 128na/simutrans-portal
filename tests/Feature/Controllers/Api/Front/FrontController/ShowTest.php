<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Front\FrontController;

use App\Enums\ArticleStatus;
use App\Models\Article;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

final class ShowTest extends TestCase
{
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->publish()->create();
    }

    public function test_ユーザーID_スラッグ(): void
    {
        $testResponse = $this->get(sprintf('api/front/users/%s/%s',
            $this->article->user_id,
            $this->article->slug
        ));

        $testResponse->assertOk();
        $testResponse->assertSee($this->article->title);
    }

    public function test_ニックネーム_スラッグ(): void
    {
        $testResponse = $this->get(sprintf('api/front/users/%s/%s',
            $this->article->user->nickname,
            $this->article->slug
        ));

        $testResponse->assertOk();
    }

    #[DataProvider('shouldHideStatuses')]
    public function test_非公開(ArticleStatus $articleStatus): void
    {
        $this->article->update(['status' => $articleStatus]);
        $testResponse = $this->get(sprintf('api/front/users/%s/%s',
            $this->article->user_id,
            $this->article->slug
        ));

        $testResponse->assertNotFound();
    }

    public static function shouldHideStatuses(): \Generator
    {
        yield '非公開' => [ArticleStatus::Private];
        yield '下書き' => [ArticleStatus::Draft];
        yield 'ゴミ箱' => [ArticleStatus::Trash];
        yield '予約投稿' => [ArticleStatus::Reservation];
    }
}
