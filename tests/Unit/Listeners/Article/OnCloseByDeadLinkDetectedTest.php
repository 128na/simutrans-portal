<?php

declare(strict_types=1);

namespace Tests\Unit\Listeners\Article;

use App\Events\Article\CloseByDeadLinkDetected;
use App\Listeners\Article\OnCloseByDeadLinkDetected;
use App\Models\Article;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Tests\Unit\TestCase;

class OnCloseByDeadLinkDetectedTest extends TestCase
{
    public function test_legacy_event_records_the_article_in_audit_log(): void
    {
        Log::shouldReceive('channel')->with('audit')->andReturnSelf();
        $context = null;
        Log::shouldReceive('info')->with('記事非公開化', \Mockery::type('array'))->andReturnUsing(function (string $message, array $values) use (&$context): void {
            $context = $values;
        });
        $article = (new Article)->forceFill(['id' => 1, 'title' => '対象記事']);
        (new OnCloseByDeadLinkDetected)->handle(new CloseByDeadLinkDetected($article));
        $this->assertSame(['articleId' => 1, 'articleTitle' => '対象記事'], $context);
    }

    public function test_legacy_event_does_not_send_an_individual_email(): void
    {
        Log::shouldReceive('channel')->with('audit')->andReturnSelf();
        Log::shouldReceive('info')->with('記事非公開化', \Mockery::type('array'))->andReturnNull();
        Notification::fake();
        $article = (new Article)->forceFill(['id' => 2, 'title' => '対象記事']);
        (new OnCloseByDeadLinkDetected)->handle(new CloseByDeadLinkDetected($article));
        Notification::assertNothingSent();
    }

    public function test_repeated_legacy_event_does_not_create_duplicate_emails(): void
    {
        Log::shouldReceive('channel')->with('audit')->andReturnSelf();
        Log::shouldReceive('info')->with('記事非公開化', \Mockery::type('array'))->andReturnNull();
        Notification::fake();
        $event = new CloseByDeadLinkDetected((new Article)->forceFill(['id' => 3, 'title' => '対象記事']));
        $listener = new OnCloseByDeadLinkDetected;
        $listener->handle($event);
        $listener->handle($event);
        Notification::assertNothingSent();
        Log::shouldHaveReceived('info')->twice();
    }
}
