<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\Article;

use App\Events\Article\CloseByDeadLinkDetected;
use App\Listeners\Article\OnCloseByDeadLinkDetected;
use App\Models\Article;
use App\Notifications\SendDeadLinkDetectedEmail;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCase;

class OnCloseByDeadLinkDetectedTest extends TestCase
{
    #[Test]
    public function it_sends_notification_to_article(): void
    {
        Notification::fake();

        $article = Article::factory()->make(['id' => 1]);

        $listener = new OnCloseByDeadLinkDetected;
        $event = new CloseByDeadLinkDetected($article);

        $listener->handle($event);

        Notification::assertSentTo($article, SendDeadLinkDetectedEmail::class);
    }

    #[Test]
    public function it_sends_correct_notification_type(): void
    {
        Notification::fake();

        $article = Article::factory()->make(['id' => 2]);

        $listener = new OnCloseByDeadLinkDetected;
        $event = new CloseByDeadLinkDetected($article);

        $listener->handle($event);

        Notification::assertSentTo(
            $article,
            function (SendDeadLinkDetectedEmail $notification) {
                return $notification instanceof SendDeadLinkDetectedEmail;
            }
        );
    }

    #[Test]
    public function it_sends_only_one_notification(): void
    {
        Notification::fake();

        $article = Article::factory()->make(['id' => 3]);

        $listener = new OnCloseByDeadLinkDetected;
        $event = new CloseByDeadLinkDetected($article);

        $listener->handle($event);

        Notification::assertCount(1);
    }
}
