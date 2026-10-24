<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\ArticleClosure\CloseArticle;
use App\Actions\ArticleClosure\Recover;
use App\Actions\ArticleClosure\Runs;
use App\Actions\ArticleClosure\SendBatch;
use App\Enums\ArticleClosureBatchStatus;
use App\Enums\ArticleClosureReason;
use App\Enums\ArticleClosureRunStatus;
use App\Jobs\SendArticleClosureDigest;
use App\Models\Article;
use App\Models\ArticleClosureBatch;
use App\Models\ArticleClosureRun;
use App\Models\User;
use App\Notifications\ArticleClosureDigest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Transport\ArrayTransport;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use RuntimeException;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;
use Symfony\Component\Mime\Email;

class ArticleClosureDeliveryTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        config(['mail.default' => 'array', 'article-closure.mailer' => null, 'mail.mailers.article_closure_digest' => ['transport' => 'array']]);
        Mail::purge('article_closure_digest');
    }

    public function test_actual_mail_groups_each_owners_snapshots_and_deduplicates_jobs(): void
    {
        $run = $this->createRun();
        $a = $this->batch($run, count: 3);
        $b = $this->batch($run, count: 2);
        $empty = $this->batch($run, count: 0);

        foreach ([$a, $b, $empty, $a, $b] as $batch) {
            (new SendArticleClosureDigest($batch->id))->handle(app(SendBatch::class));
        }
        $messages = $this->transport()->messages();
        $this->assertCount(2, $messages);
        foreach ([[$a, 3, 0], [$b, 2, 1]] as [$batch, $count, $index]) {
            $mail = $messages[$index]->getOriginalMessage();
            $this->assertInstanceOf(Email::class, $mail);
            $this->assertSame($batch->user->email, $mail->getTo()[0]->getAddress());
            $this->assertSame("リンク切れ等により{$count}件の記事を非公開にしました", $mail->getSubject());
            foreach ($batch->items as $item) {
                $this->assertStringContainsString(e($item->title), $mail->getHtmlBody());
                $this->assertStringContainsString(e($item->url), $mail->getHtmlBody());
                $this->assertStringContainsString(e($item->edit_url), $mail->getHtmlBody());
            }
            $other = $batch->id === $a->id ? $b : $a;
            $this->assertStringNotContainsString(e($other->items->first()->title), $mail->getHtmlBody());
            $this->assertSame(ArticleClosureBatchStatus::Sent, $batch->fresh()->status);
            $this->assertSame(1, $batch->fresh()->attempts);
            $this->assertNotNull($batch->fresh()->sent_at);
        }
        $this->assertSame(ArticleClosureBatchStatus::Skipped, $empty->fresh()->status);
    }

    public function test_mail_escapes_snapshot_and_explains_reasons_without_claiming_current_state(): void
    {
        $batch = $this->batch($this->createRun(), count: 2);
        $batch->items()->firstOrFail()->update(['title' => '<script>alert("x")</script>',
            'url' => 'https://example.net/?a="><script>x</script>', 'reason' => ArticleClosureReason::ServiceClosed]);
        app(SendBatch::class)($batch->id);
        $mail = $this->transport()->messages()->first()->getOriginalMessage();
        $html = $mail->getHtmlBody();
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('配布先サービスの終了', $html);
        $this->assertStringContainsString('通常のリンク切れチェック', $html);
        $this->assertStringContainsString('現在の記事の公開状態とは異なる場合があります', $html);
        $this->assertStringContainsString('記載日時に非公開にしました', $html);
        $this->assertStringContainsString('投稿記事へ変換', $html);
        $this->assertStringContainsString('自動リンク切れチェックは記事編集から無効化できます', $html);
    }

    public function test_service_closed_only_mail_does_not_suggest_disabling_deadlink_check(): void
    {
        $batch = $this->batch($this->createRun());
        $batch->items()->update(['reason' => ArticleClosureReason::ServiceClosed]);
        app(SendBatch::class)($batch->id);
        $html = $this->transport()->messages()->first()->getOriginalMessage()->getHtmlBody();
        $this->assertStringContainsString('配布先サービスの終了', $html);
        $this->assertStringNotContainsString('無効化できます', $html);
    }

    public function test_sending_is_durable_before_smtp_and_duplicate_worker_does_not_send(): void
    {
        $batch = $this->batch($this->createRun());
        $transactionLevel = DB::transactionLevel();
        $calls = 0;
        Event::listen(MessageSending::class, function () use ($batch, $transactionLevel, &$calls): void {
            $calls++;
            $claimed = $batch->fresh();
            $this->assertSame(ArticleClosureBatchStatus::Sending, $claimed->status);
            $this->assertNotNull($claimed->sending_token);
            $this->assertNull($claimed->dispatch_lease_until);
            $this->assertNotNull($claimed->sending_at);
            $this->assertSame($transactionLevel, DB::transactionLevel());
            app(SendBatch::class)($batch->id);
        });
        app(SendBatch::class)($batch->id);
        $this->assertSame(1, $calls);
        $this->assertCount(1, $this->transport()->messages());
    }

    public function test_preparation_failures_retry_only_due_batches_and_stop_at_limit(): void
    {
        $batch = $this->batch($this->createRun());
        $batch->update(['dispatch_lease_until' => now()->addSeconds(900)]);
        config(['article-closure.mailer' => 'missing-mailer']);
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            app(SendBatch::class)($batch->id);
            $failed = $batch->fresh();
            $this->assertSame(ArticleClosureBatchStatus::Failed, $failed->status);
            $this->assertSame($attempt, $failed->attempts);
            $this->assertStringContainsString('configuration is missing', $failed->last_error);
            $this->assertNull($failed->sending_at);
            $this->assertNull($failed->dispatch_lease_until);
            app(SendBatch::class)($batch->id);
            $this->assertSame($attempt, $batch->fresh()->attempts);
            $this->travel(61 * $attempt)->seconds();
        }
        $this->assertNull($batch->fresh()->next_attempt_at);
        Queue::fake();
        app(Recover::class)();
        Queue::assertNothingPushed();
    }

    public function test_preparation_failure_is_recoverable_without_reclosing_articles(): void
    {
        $batch = $this->batch($this->createRun());
        $snapshotIds = $batch->items()->pluck('id')->all();
        $batch->update(['dispatch_lease_until' => now()->addSeconds(900)]);
        config(['article-closure.mailer' => 'missing-mailer']);
        app(SendBatch::class)($batch->id);
        config(['article-closure.mailer' => null]);
        $this->travel(61)->seconds();
        app(SendBatch::class)($batch->id);
        $this->assertSame(ArticleClosureBatchStatus::Sent, $batch->fresh()->status);
        $this->assertSame(2, $batch->fresh()->attempts);
        $this->assertSame($snapshotIds, $batch->items()->pluck('id')->all());
        $this->assertCount(1, $this->transport()->messages());
    }

    public function test_delivery_exception_is_unknown_and_other_owner_still_sends(): void
    {
        $run = $this->createRun();
        $first = $this->batch($run);
        $second = $this->batch($run);
        Event::listen(MessageSending::class, function (MessageSending $event) use ($first): void {
            if ($event->message->getTo()[0]->getAddress() === $first->user->email) {
                throw new RuntimeException('SMTP result uncertain');
            }
        });
        app(SendBatch::class)($first->id);
        app(SendBatch::class)($first->id);
        app(SendBatch::class)($second->id);
        $this->assertSame(ArticleClosureBatchStatus::Unknown, $first->fresh()->status);
        $this->assertStringContainsString('SMTP result uncertain', $first->fresh()->last_error);
        $this->assertSame(1, $first->fresh()->attempts);
        $this->assertSame(ArticleClosureBatchStatus::Sent, $second->fresh()->status);
        $this->assertCount(1, $this->transport()->messages());
    }

    public function test_old_sender_cannot_overwrite_a_replaced_attempt_token(): void
    {
        $batch = $this->batch($this->createRun());
        Event::listen(MessageSending::class, function () use ($batch): void {
            $batch->fresh()->update(['sending_token' => '11111111-1111-4111-8111-111111111111']);
        });
        app(SendBatch::class)($batch->id);
        $this->assertSame(ArticleClosureBatchStatus::Sending, $batch->fresh()->status);
        $this->assertNull($batch->fresh()->sent_at);
        $this->assertSame('11111111-1111-4111-8111-111111111111', $batch->fresh()->sending_token);
    }

    public function test_deleted_or_missing_recipient_is_skipped_and_running_run_is_not_sent(): void
    {
        $run = $this->createRun();
        $deleted = $this->batch($run);
        $deleted->user->delete();
        $missing = $this->batch($run);
        $missing->user->forceDelete();
        $pending = $this->batch($this->createRun(finalized: false));
        foreach ([$deleted, $missing, $pending] as $batch) {
            app(SendBatch::class)($batch->id);
        }
        $this->assertSame(ArticleClosureBatchStatus::Skipped, $deleted->fresh()->status);
        $this->assertSame(ArticleClosureBatchStatus::Skipped, $missing->fresh()->status);
        $this->assertSame(ArticleClosureBatchStatus::Pending, $pending->fresh()->status);
        $this->assertCount(0, $this->transport()->messages());
    }

    public function test_recovery_finalizes_stale_runs_marks_stale_delivery_unknown_and_dispatches_due_only(): void
    {
        $old = $this->createRun(finalized: false);
        $old->update(['heartbeat_at' => now()->subSeconds(301)]);
        $oldBatch = $this->batch($old);
        $healthy = $this->createRun(finalized: false);
        $healthyBatch = $this->batch($healthy);
        $run = $this->createRun();
        $sending = $this->batch($run);
        $sending->update(['status' => ArticleClosureBatchStatus::Sending, 'sending_at' => now()->subSeconds(121)]);
        $future = $this->batch($run);
        $future->update(['status' => ArticleClosureBatchStatus::Failed, 'next_attempt_at' => now()->addMinutes(2)]);
        $exhausted = $this->batch($run);
        $exhausted->update(['status' => ArticleClosureBatchStatus::Failed, 'attempts' => 3]);
        $due = $this->batch($run);
        $due->update(['status' => ArticleClosureBatchStatus::Failed, 'attempts' => 1, 'next_attempt_at' => now()->subSecond()]);
        Queue::fake();
        app(Recover::class)();
        $this->assertSame(ArticleClosureRunStatus::Interrupted, $old->fresh()->status);
        $this->assertSame(ArticleClosureRunStatus::Running, $healthy->fresh()->status);
        $this->assertSame(ArticleClosureBatchStatus::Unknown, $sending->fresh()->status);
        Queue::assertPushed(SendArticleClosureDigest::class, 2);
        Queue::assertPushed(SendArticleClosureDigest::class, fn (SendArticleClosureDigest $job): bool => $job->batchId === $oldBatch->id);
        Queue::assertPushed(SendArticleClosureDigest::class, fn (SendArticleClosureDigest $job): bool => $job->batchId === $due->id);
        Queue::assertNotPushed(SendArticleClosureDigest::class, fn (SendArticleClosureDigest $job): bool => in_array($job->batchId, [$sending->id, $healthyBatch->id, $future->id, $exhausted->id], true));
    }

    public function test_recovery_dispatch_count_is_bounded(): void
    {
        $run = $this->createRun();
        for ($i = 0; $i < 3; $i++) {
            $this->batch($run);
        }
        config(['article-closure.recovery_limit' => 2]);
        Queue::fake();
        $this->artisan('article:closure-notifications --recover')->assertSuccessful();
        Queue::assertPushed(SendArticleClosureDigest::class, 2);
    }

    public function test_cli_explicit_retry_resets_unknown_and_failed_but_refuses_sent_sending_and_unfinalized(): void
    {
        $run = $this->createRun();
        Queue::fake();
        foreach ([ArticleClosureBatchStatus::Unknown, ArticleClosureBatchStatus::Failed] as $status) {
            $batch = $this->batch($run);
            $batch->update(['status' => $status, 'attempts' => 3, 'dispatch_lease_until' => now()->addSeconds(900), 'sending_token' => '11111111-1111-4111-8111-111111111111', 'last_error' => 'failed']);
            $this->artisan('article:closure-notifications', ['--retry' => $batch->id])->assertSuccessful();
            $reset = $batch->fresh();
            $this->assertSame(ArticleClosureBatchStatus::Pending, $reset->status);
            $this->assertSame(0, $reset->attempts);
            $this->assertNull($reset->sending_token);
            $this->assertNull($reset->dispatch_lease_until);
            $this->assertNull($reset->last_error);
        }
        foreach ([ArticleClosureBatchStatus::Sent, ArticleClosureBatchStatus::Sending, ArticleClosureBatchStatus::Pending, ArticleClosureBatchStatus::Skipped] as $status) {
            $batch = $this->batch($run);
            $batch->update(['status' => $status]);
            $this->artisan('article:closure-notifications', ['--retry' => $batch->id])->assertFailed();
            $this->assertSame($status, $batch->fresh()->status);
        }
        $running = $this->batch($this->createRun(finalized: false));
        $running->update(['status' => ArticleClosureBatchStatus::Unknown]);
        $this->artisan('article:closure-notifications', ['--retry' => $running->id])->assertFailed();
        $this->artisan('article:closure-notifications --retry=invalid')->assertFailed();
        $this->artisan('article:closure-notifications --status')->expectsOutputToContain('unknown')->assertSuccessful();
        Queue::assertPushed(SendArticleClosureDigest::class, 2);
    }

    public function test_delivery_uses_snapshots_after_articles_are_edited_republished_or_deleted(): void
    {
        $user = User::factory()->create();
        $articles = [];
        foreach (['Original published title', 'Original deleted title'] as $title) {
            $articles[] = Article::factory()->publish()->create([
                'user_id' => $user->id, 'post_type' => 'addon-introduction', 'title' => $title,
                'contents' => ['link' => 'https://example.net/original.zip', 'author' => 'Author', 'description' => 'Addon'],
            ]);
        }
        Bus::fake();
        $runs = app(Runs::class);
        $run = $runs->start();
        foreach ($articles as $article) {
            $this->assertTrue(app(CloseArticle::class)($article, $run->id, ArticleClosureReason::DeadLink));
        }
        $articles[0]->refresh()->update(['title' => 'Changed current title', 'status' => 'publish',
            'contents' => ['link' => 'https://example.net/moved.zip', 'author' => 'Author', 'description' => 'Updated']]);
        $articles[1]->refresh()->forceDelete();
        $runs->finalize($run->id);
        $batch = ArticleClosureBatch::where('run_id', $run->id)->firstOrFail();
        app(SendBatch::class)($batch->id);
        $mail = $this->transport()->messages()->first()->getOriginalMessage();
        $this->assertSame($user->email, $mail->getTo()[0]->getAddress());
        $html = $mail->getHtmlBody();
        $this->assertStringContainsString('Original published title', $html);
        $this->assertStringContainsString('Original deleted title', $html);
        $this->assertStringContainsString('https://example.net/original.zip', $html);
        $this->assertStringNotContainsString('Changed current title', $html);
        $this->assertStringNotContainsString('https://example.net/moved.zip', $html);
        $this->assertStringContainsString('現在の記事の公開状態とは異なる場合があります', $html);
        $this->assertSame(2, $batch->items()->count());
    }

    public function test_dedicated_smtp_timeout_is_bounded_without_changing_existing_mailer(): void
    {
        $batch = $this->batch($this->createRun());
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.timeout' => null]);
        $source = config('mail.mailers.smtp');
        $inspected = false;
        Event::listen(MessageSending::class, function () use (&$inspected, $source): void {
            $inspected = true;
            $this->assertSame($source, config('mail.mailers.smtp'));
            $this->assertSame(30, config('mail.mailers.article_closure_digest.timeout'));
            $transport = Mail::mailer('article_closure_digest')->getSymfonyTransport();
            $this->assertInstanceOf(EsmtpTransport::class, $transport);
            $stream = $transport->getStream();
            $this->assertInstanceOf(SocketStream::class, $stream);
            $this->assertSame(30.0, $stream->getTimeout());
            throw new RuntimeException('Test stopped delivery before SMTP connection.');
        });
        app(SendBatch::class)($batch->id);
        $this->assertTrue($inspected);
        $this->assertSame(ArticleClosureBatchStatus::Unknown, $batch->fresh()->status);
        $this->assertNotInstanceOf(ShouldQueue::class,
            new ArticleClosureDigest(1, 'Prepared HTML'));
        $job = new SendArticleClosureDigest($batch->id);
        $this->assertLessThan(config('article-closure.sending_timeout'), $job->timeout);
        $this->assertLessThan(config('queue.connections.database.retry_after'), config('article-closure.sending_timeout'));
    }

    public function test_recovery_leases_first_hundred_so_next_run_advances_to_following_hundred(): void
    {
        $run = $this->createRun();
        $ids = [];
        for ($i = 0; $i < 201; $i++) {
            $ids[] = $this->batch($run, count: 0)->id;
        }
        Queue::fake();
        app(Recover::class)();
        Queue::assertPushed(SendArticleClosureDigest::class, 100);
        app(Recover::class)();
        Queue::assertPushed(SendArticleClosureDigest::class, 200);
        $dispatched = Queue::pushed(SendArticleClosureDigest::class)->pluck('batchId')->all();
        $this->assertSame(array_slice($ids, 0, 200), $dispatched);
        $this->assertSame(200, ArticleClosureBatch::where('dispatch_lease_until', '>', now())->count());
        app(Recover::class)();
        app(Recover::class)();
        Queue::assertPushed(SendArticleClosureDigest::class, 201);
    }

    public function test_dispatch_lease_expiry_recovers_a_job_lost_before_processing(): void
    {
        $batch = $this->batch($this->createRun());
        Queue::fake();
        app(Recover::class)();
        $lease = $batch->fresh()->dispatch_lease_until;
        $this->assertNotNull($lease);
        app(Recover::class)();
        Queue::assertPushed(SendArticleClosureDigest::class, 1);
        $this->travel(901)->seconds();
        app(Recover::class)();
        Queue::assertPushed(SendArticleClosureDigest::class, 2);
        $this->assertTrue($batch->fresh()->dispatch_lease_until->greaterThan($lease));
        $this->assertSame(ArticleClosureBatchStatus::Pending, $batch->fresh()->status);
    }

    public function test_dispatch_failure_is_recorded_and_does_not_stop_other_batches_or_spin_until_lease_expires(): void
    {
        $run = $this->createRun();
        $first = $this->batch($run);
        $second = $this->batch($run);
        $originalBus = Bus::getFacadeRoot();
        $pushed = [];
        Bus::partialMock()->shouldReceive('dispatch')->andReturnUsing(function (SendArticleClosureDigest $job) use (&$pushed, $first): void {
            $pushed[] = $job->batchId;
            if ($job->batchId === $first->id) {
                throw new RuntimeException('Queue temporarily unavailable');
            }
        });
        app(Recover::class)();
        $this->assertSame([$first->id, $second->id], $pushed);
        $this->assertSame(ArticleClosureBatchStatus::Pending, $first->fresh()->status);
        $this->assertStringContainsString('Queue temporarily unavailable', $first->fresh()->last_error);
        $this->assertNotNull($first->fresh()->dispatch_lease_until);
        app(Recover::class)();
        $this->assertSame([$first->id, $second->id], $pushed);
        Bus::swap($originalBus);
        Queue::fake();
        $this->travel(901)->seconds();
        app(Recover::class)();
        Queue::assertPushed(SendArticleClosureDigest::class, 2);
        Queue::assertPushed(SendArticleClosureDigest::class, fn (SendArticleClosureDigest $job): bool => $job->batchId === $first->id);
    }

    public function test_worker_processes_a_leased_batch_and_clears_the_dispatch_lease(): void
    {
        $batch = $this->batch($this->createRun());
        $batch->update(['dispatch_lease_until' => now()->addSeconds(900)]);
        (new SendArticleClosureDigest($batch->id))->handle(app(SendBatch::class));
        $this->assertSame(ArticleClosureBatchStatus::Sent, $batch->fresh()->status);
        $this->assertNull($batch->fresh()->dispatch_lease_until);
        $this->assertCount(1, $this->transport()->messages());
    }

    private function createRun(bool $finalized = true): ArticleClosureRun
    {
        return ArticleClosureRun::create(['kind' => ArticleClosureReason::DeadLink,
            'status' => $finalized ? ArticleClosureRunStatus::Completed : ArticleClosureRunStatus::Running,
            'started_at' => now(), 'heartbeat_at' => now(), 'finalized_at' => $finalized ? now() : null]);
    }

    private function batch(ArticleClosureRun $run, int $count = 1): ArticleClosureBatch
    {
        $batch = ArticleClosureBatch::create(['run_id' => $run->id, 'user_id' => User::factory()->create()->id]);
        for ($i = 0; $i < $count; $i++) {
            $batch->items()->create(['article_id' => $batch->id * 10 + $i, 'title' => "Batch {$batch->id} article {$i}",
                'url' => "https://example.net/batch-{$batch->id}-{$i}.zip", 'edit_url' => "https://portal.example/mypage/articles/edit/{$batch->id}{$i}",
                'reason' => ArticleClosureReason::DeadLink, 'closed_at' => now()]);
        }

        return $batch;
    }

    private function transport(): ArrayTransport
    {
        $transport = Mail::mailer('article_closure_digest')->getSymfonyTransport();
        $this->assertInstanceOf(ArrayTransport::class, $transport);

        return $transport;
    }
}
