<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\DeadLink;

use App\Actions\DeadLink\FailedCountCache;
use App\Actions\DeadLink\OnDead;
use App\Enums\ArticleStatus;
use App\Events\Article\DeadLinkDetected;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;
use Tests\Unit\TestCase;

final class OnDeadTest extends TestCase
{
    public function test_2回目まで(): void
    {
        $article = new Article;
        $this->mock(FailedCountCache::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->get($article)->once()->andReturn(1);
            $mock->expects()->update($article, 2)->once();
        });

        Event::fake();
        $actual = $this->getSUT()($article);
        $this->assertFalse($actual);
        Event::assertDispatched(DeadLinkDetected::class);
    }

    public function test_3回目(): void
    {
        $article = new Article;
        $this->mock(FailedCountCache::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->get($article)->once()->andReturn(2);
            $mock->expects()->clear($article)->once();
        });
        $this->mock(ArticleRepository::class, function (MockInterface $mock) use ($article): void {
            $mock->expects()->update($article, ['status' => ArticleStatus::Private]);
        });

        Event::fake();
        $actual = $this->getSUT()($article);
        $this->assertTrue($actual);
        Event::assertDispatched(DeadLinkDetected::class);
    }

    private function getSUT(): OnDead
    {
        return app(OnDead::class);
    }
}
