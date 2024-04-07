<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\Article;

use App\Events\Article\ArticleStored;
use App\Listeners\Article\OnArticleStored;
use App\Models\Article;
use App\Notifications\SendArticlePublished;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

class OnArticleStoredTest extends TestCase
{
    private function getSUT(): OnArticleStored
    {
        return app(OnArticleStored::class);
    }

    #[DataProvider('data')]
    public function test(bool $isPublish, bool $shouldNotify, bool $expectNotify): void
    {
        /** @var Article&MockInterface */
        $mock = $this->mock(Article::class, function (MockInterface $mock) use ($isPublish, $expectNotify): void {
            $mock->allows()->getAttribute('is_publish')->andReturn($isPublish);
            $mock->allows()->getInfoLogging()->andReturn([]);
            if ($expectNotify) {
                $mock->expects()->notify(SendArticlePublished::class)->once();
            } else {
                $mock->expects()->notify(SendArticlePublished::class)->never();
            }
        });

        $articleStored = new ArticleStored($mock, $shouldNotify);
        $result = $this->getSUT()->handle($articleStored);
        $this->assertNull($result);
    }

    public static function data(): \Generator
    {
        yield '公開,通知ON->投稿通知' => [true, true, true];
        yield '公開,通知OFF->通知しない' => [true, false, false];
        yield '公開以外,通知ON->通知しない' => [false, true, false];
        yield '公開以外,通知OFF->通知しない' => [false, false, false];
    }
}
