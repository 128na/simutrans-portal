<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\Article;

use App\Events\Article\ArticleUpdated;
use App\Listeners\Article\OnArticleUpdated;
use App\Models\Article;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

final class OnArticleUpdatedTest extends TestCase
{
    #[DataProvider('data')]
    public function test(
        bool $notYetPublished,
        bool $isPublish,
        bool $shouldNotify,
        bool $expectPublishNotify,
        bool $expectUpdateNotify,
    ): void {
        /** @var Article&MockInterface */
        $mock = $this->mock(Article::class, function (MockInterface $mock) use (
            $isPublish,
            $expectPublishNotify,
            $expectUpdateNotify,
        ): void {
            $mock->allows()->getAttribute('is_publish')->andReturn($isPublish);
            $mock->allows()->getInfoLogging()->andReturn([]);
            if ($expectPublishNotify) {
                $mock->expects()->notify(SendArticlePublished::class)->once();
            } else {
                $mock->expects()->notify(SendArticlePublished::class)->never();
            }

            if ($expectUpdateNotify) {
                $mock->expects()->notify(SendArticleUpdated::class)->once();
            } else {
                $mock->expects()->notify(SendArticleUpdated::class)->never();
            }
        });

        $articleUpdated = new ArticleUpdated($mock, $shouldNotify, $notYetPublished);
        $result = $this->getSUT()->handle($articleUpdated);
        $this->assertNull($result);
    }

    public static function data(): \Generator
    {
        yield '未公開,公開,通知ON->投稿通知' => [true, true, true, true, false];
        yield '未公開,公開,通知OF->通知しない' => [true, true, false, false, false];
        yield '未公開,公開以外,通知ON->通知しない' => [true, false, true, false, false];
        yield '未公開,公開以外,通知OF->通知しない' => [true, false, false, false, false];
        yield '公開済,公開,通知ON->更新通知' => [false, true, true, false, true];
        yield '公開済,公開,通知OF->通知しない' => [false, true, false, false, false];
        yield '公開済,公開以外,通知ON->通知しない' => [false, false, true, false, false];
        yield '公開済,公開以外,通知OF->通知しない' => [false, false, false, false, false];
    }

    private function getSUT(): OnArticleUpdated
    {
        return app(OnArticleUpdated::class);
    }
}
