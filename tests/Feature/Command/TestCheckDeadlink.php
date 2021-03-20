<?php

namespace Tests\Feature\Command;

use App\Models\Article;
use App\Notifications\DeadLinkDetected;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TestCheckDeadlink extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->create([
            'user_id' => $this->user->id,
            'post_type' => 'addon-introduction',
            'status' => 'publish',
            'contents' => [
                'link' => config('app.url').'/not_found_url',
                'exclude_link_check' => false,
            ],
        ]);
    }

    public function test実行()
    {
        Notification::fake();
        Notification::assertNothingSent();

        $this->artisan('check:deadlink')
            ->assertExitCode(0);

        Notification::assertSentTo($this->article, DeadLinkDetected::class);
    }

    public function testオプション無効だとチェックしない()
    {
        Notification::fake();
        $this->article->fill(['contents' => [
            'link' => config('app.url').'/not_found_url',
            'exclude_link_check' => true,
        ]])->save();

        Notification::assertNothingSent();

        $this->artisan('check:deadlink')
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }

    /**
     * @dataProvider dataStatus
     */
    public function test非公開記事はチェックしない(string $status)
    {
        Notification::fake();
        $this->article->fill(['status' => $status])->save();

        Notification::assertNothingSent();

        $this->artisan('check:deadlink')
            ->assertExitCode(0);

        Notification::assertNothingSent();
    }

    public function dataStatus()
    {
        yield '下書き' => ['draft'];
        yield '非公開' => ['private'];
        yield 'ゴミ箱' => ['trash'];
    }
}
