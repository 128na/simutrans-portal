<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Article;

use App\Enums\ArticleStatus;
use App\Events\Article\CloseByDeadLinkDetected;
use App\Jobs\Article\JobCheckDeadLink;
use App\Models\Article;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\TestCase;

final class JobCheckDeadlinkTest extends TestCase
{
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->addonIntroduction()->publish()->create([
            'contents' => [
                'link' => '/not_found_url',
                'exclude_link_check' => false,
            ],
        ]);
    }

    public function test実行(): void
    {
        Event::fake();
        Event::assertNothingDispatched();

        JobCheckDeadLink::dispatchSync();
        Event::assertNotDispatched(CloseByDeadLinkDetected::class);

        JobCheckDeadLink::dispatchSync();
        Event::assertNotDispatched(CloseByDeadLinkDetected::class);

        JobCheckDeadLink::dispatchSync();
        Event::assertDispatched(CloseByDeadLinkDetected::class);
    }

    public function testオプション無効だとチェックしない(): void
    {
        Notification::fake();
        $this->article->fill(['contents' => [
            'link' => config('app.url').'/not_found_url',
            'exclude_link_check' => true,
        ]])->save();

        Notification::assertNothingSent();

        JobCheckDeadLink::dispatchSync();

        Notification::assertNothingSent();
    }

    public function test非公開記事はチェックしない(): void
    {
        Notification::fake();
        $this->article->update(['status' => ArticleStatus::Private]);

        Notification::assertNothingSent();

        JobCheckDeadLink::dispatchSync();

        Notification::assertNothingSent();
    }
}
