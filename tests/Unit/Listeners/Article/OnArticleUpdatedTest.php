<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\Article;

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

        $articleUpdated = new ArticleUpdated($mock, $shouldNotify, $notYetPublished);
        $result = $this->getSUT()->handle($articleUpdated);
        $this->assertNull($result);
    }

    public static function data(): \Generator
    {
        yield '未公開,公開,投稿通知ON' => [true, true, true, true];
        yield '未公開,公開,投稿通知OF' => [true, true, false, false];
        yield '未公開,非公開,投稿通知ON' => [true, false, true, false];
        yield '未公開,非公開,投稿通知OF' => [true, false, false, false];
        yield '公開済,公開,投稿通知ON' => [false, true, true, false];
        yield '公開済,公開,投稿通知OF' => [false, true, false, false];
        yield '公開済,非公開,投稿通知ON' => [false, false, true, false];
        yield '公開済,非公開,投稿通知OF' => [false, false, false, false];
    }
}
