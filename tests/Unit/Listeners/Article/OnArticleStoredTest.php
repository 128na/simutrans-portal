<?php

declare(strict_types=1);

namespace Tests\Listeners\Article;

use App\Events\Article\ArticleStored;
use App\Listeners\Article\OnArticleStored;
use App\Models\Article;
use App\Notifications\SendArticlePublished;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

class OnArticleStoredTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

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

        $screenshotStored = new ArticleStored($mock, $shouldNotify);
        $result = $this->getSUT()->handle($screenshotStored);
        $this->assertNull($result);
    }

    public static function data(): \Generator
    {
        yield '公開,投稿通知ON' => [T, T, T];
        yield '公開,投稿通知OFF' => [T, F, F];
        yield '公開以外,投稿通知ON' => [F, T, F];
        yield '公開以外,投稿通知OFF' => [F, F, F];
    }
}
