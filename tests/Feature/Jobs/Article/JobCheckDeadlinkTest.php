<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Article;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Jobs\Article\JobCheckDeadLink;
use App\Models\Article;
use App\Notifications\SendDeadLinkDetectedEmail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class JobCheckDeadlinkTest extends TestCase
{
    private $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->create([
            'user_id' => $this->user->id,
            'post_type' => ArticlePostType::AddonIntroduction,
            'status' => ArticleStatus::Publish,
            'contents' => [
                'link' => config('app.url').'/not_found_url',
                'exclude_link_check' => false,
            ],
        ]);
    }

    public function test実行(): void
    {
        Notification::fake();
        Notification::assertNothingSent();

        JobCheckDeadLink::dispatchSync();
        Notification::assertNothingSent();

        JobCheckDeadLink::dispatchSync();
        Notification::assertNothingSent();

        JobCheckDeadLink::dispatchSync();
        Notification::assertSentTo($this->article, SendDeadLinkDetectedEmail::class);
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

    /**
     * @dataProvider dataStatusPrivate
     */
    public function test非公開記事はチェックしない(string $status): void
    {
        Notification::fake();
        $this->article->fill(['status' => $status])->save();

        Notification::assertNothingSent();

        JobCheckDeadLink::dispatchSync();

        Notification::assertNothingSent();
    }
}
