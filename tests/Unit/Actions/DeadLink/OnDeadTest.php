<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\DeadLink;

use App\Actions\ArticleClosure\CloseArticle;
use App\Actions\DeadLink\OnDead;
use App\Enums\ArticleClosureReason;
use App\Events\Article\DeadLinkDetected;
use App\Models\Article;
use App\Repositories\ArticleLinkCheckHistoryRepository;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

class OnDeadTest extends TestCase
{
    public function test_2回目まで(): void
    {
        $article = new Article;
        $this->mock(ArticleLinkCheckHistoryRepository::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->increment($article)->once();
            $mock->expects()->get($article)->once()->andReturn(2);
        });

        Event::fake();
        $actual = $this->getSUT()($article, 1);
        $this->assertFalse($actual);
        Event::assertDispatched(DeadLinkDetected::class);
    }

    public function test_3回目(): void
    {
        $article = new Article;
        $this->mock(ArticleLinkCheckHistoryRepository::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->increment($article)->once();
            $mock->expects()->get($article)->once()->andReturn(3);
        });
        $this->mock(CloseArticle::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->__invoke($article, 1, ArticleClosureReason::DeadLink)->once()->andReturn(true);
        });

        Event::fake();
        $actual = $this->getSUT()($article, 1);
        $this->assertTrue($actual);
        Event::assertDispatched(DeadLinkDetected::class);
    }

    private function getSUT(): OnDead
    {
        return app(OnDead::class);
    }
}
