<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\Article;

use App\Events\Article\CloseByDeadLinkDetected;
use App\Listeners\Article\OnCloseByDeadLinkDetected;
use App\Models\Article;
use App\Notifications\SendDeadLinkDetectedEmail;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCase;

class OnCloseByDeadLinkDetectedTest extends TestCase
{
    #[Test]
    public function it_sends_notification_to_article(): void
    {
        $article = Mockery::mock(Article::class)->shouldAllowMockingProtectedMethods();
        $article->shouldReceive('setAttribute')->andReturnNull();
        $article->shouldReceive('notify')->once()->with(Mockery::type(SendDeadLinkDetectedEmail::class));
        $article->id = 1;

        $listener = new OnCloseByDeadLinkDetected;
        $event = new CloseByDeadLinkDetected($article);
        $listener->handle($event);

        // モックの期待値が満たされることを確認
        $this->assertTrue(true);
    }

    #[Test]
    public function it_sends_correct_notification_type(): void
    {
        $notificationReceived = null;
        $article = Mockery::mock(Article::class)->shouldAllowMockingProtectedMethods();
        $article->shouldReceive('setAttribute')->andReturnNull();
        $article->shouldReceive('notify')->once()->with(Mockery::on(function ($notification) use (&$notificationReceived) {
            $notificationReceived = $notification;

            return $notification instanceof SendDeadLinkDetectedEmail;
        }));
        $article->id = 2;

        $listener = new OnCloseByDeadLinkDetected;
        $event = new CloseByDeadLinkDetected($article);
        $listener->handle($event);

        $this->assertInstanceOf(SendDeadLinkDetectedEmail::class, $notificationReceived);
    }

    #[Test]
    public function it_sends_only_one_notification(): void
    {
        $callCount = 0;
        $article = Mockery::mock(Article::class)->shouldAllowMockingProtectedMethods();
        $article->shouldReceive('setAttribute')->andReturnNull();
        $article->shouldReceive('notify')->once()->with(Mockery::type(SendDeadLinkDetectedEmail::class))->andReturnUsing(function () use (&$callCount) {
            $callCount++;
        });
        $article->id = 3;

        $listener = new OnCloseByDeadLinkDetected;
        $event = new CloseByDeadLinkDetected($article);
        $listener->handle($event);

        $this->assertSame(1, $callCount);
    }
}
