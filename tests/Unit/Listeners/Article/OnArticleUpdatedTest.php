<?php

declare(strict_types=1);

namespace Tests\Listeners\Article;

use App\Events\Article\ArticleUpdated;
use App\Listeners\Article\OnArticleUpdated;
use App\Models\Article;
use App\Notifications\SendArticlePublished;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

class OnArticleUpdatedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    private function getSUT(): OnArticleUpdated
    {
        return app(OnArticleUpdated::class);
    }

    #[DataProvider('data')]
    public function test(
        bool $notYetPublished,
        bool $isPublish,
        bool $shouldNotify,
        bool $expectNotify
    ): void {
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

        $screenshotStored = new ArticleUpdated($mock, $shouldNotify, $notYetPublished);
        $result = $this->getSUT()->handle($screenshotStored);
        $this->assertNull($result);
    }

    public static function data(): \Generator
    {
        yield '未公開,公開,投稿通知ON' => [T, T, T, T];
        yield '未公開,公開,投稿通知OF' => [T, T, F, F];
        yield '未公開,非公開,投稿通知ON' => [T, F, T, F];
        yield '未公開,非公開,投稿通知OF' => [T, F, F, F];
        yield '公開済,公開,投稿通知ON' => [F, T, T, F];
        yield '公開済,公開,投稿通知OF' => [F, T, F, F];
        yield '公開済,非公開,投稿通知ON' => [F, F, T, F];
        yield '公開済,非公開,投稿通知OF' => [F, F, F, F];
    }
}
