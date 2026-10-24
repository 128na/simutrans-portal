<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Actions\ArticleClosure\Recover;
use App\Actions\ArticleClosure\Runs;
use App\Actions\DeadLink\GetHeaders;
use App\Actions\DeadLink\ProcessDeadLink;
use App\Enums\ArticleClosureRunStatus;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\ArticleClosureBatch;
use App\Models\ArticleClosureItem;
use App\Models\ArticleClosureRun;
use App\Models\User;
use App\Repositories\ArticleLinkCheckHistoryRepository;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

class ProcessDeadLinkTest extends TestCase
{
    public function test_command_collects_only_closed_articles_by_user_and_never_sends_individual_mail(): void
    {
        Bus::fake();
        Notification::fake();
        $this->mock(Recover::class)->shouldReceive('__invoke')->once();
        $this->mock(GetHeaders::class)->shouldReceive('__invoke')->times(15)->andReturn(['HTTP/1.1 404 Not Found']);
        $users = User::factory()->count(2)->create();
        $articles = [];
        foreach ([3, 2] as $index => $count) {
            for ($i = 0; $i < $count; $i++) {
                $article = $this->article($users[$index]);
                $this->history()->increment($article);
                $this->history()->increment($article);
                $articles[] = $article;
            }
        }
        $this->artisan('check:deadlink')->assertSuccessful();
        $run = ArticleClosureRun::sole();
        $this->assertSame(ArticleClosureRunStatus::Completed, $run->status);
        $this->assertSame(5, ArticleClosureItem::count());
        $this->assertSame(2, ArticleClosureBatch::count());
        foreach ([3, 2] as $index => $count) {
            $batch = ArticleClosureBatch::where('user_id', $users[$index]->id)->sole();
            $this->assertSame($count, $batch->items()->count());
        }
        foreach ($articles as $article) {
            $this->assertSame(ArticleStatus::Private, $article->fresh()->status);
            $this->assertSame(0, $this->history()->get($article));
        }
        Notification::assertNothingSent();
    }

    public function test_threshold_and_stale_results_preserve_current_article_and_history(): void
    {
        Bus::fake();
        $run = app(Runs::class)->start();
        $article = $this->article(User::factory()->create());
        $this->history()->increment($article);
        $process = app(ProcessDeadLink::class);
        $this->assertFalse($process($article, $run->id));
        $this->assertSame(2, $this->history()->get($article));
        $this->assertSame(ArticleStatus::Publish, $article->fresh()->status);
        $expected = $article->fresh();
        $current = $article->fresh();
        $contents = $current->contents;
        $contents->link = 'https://new-distribution.test/addon';
        $current->update(['contents' => $contents]);
        $this->assertFalse($process($expected, $run->id));
        $process->alive($expected, $run->id);
        $this->assertSame(2, $this->history()->get($current));
        $this->assertSame(0, ArticleClosureItem::count());
        $process->alive($current->fresh(), $run->id);
        $this->assertSame(0, $this->history()->get($current));
    }

    public function test_failed_snapshot_rolls_back_threshold_increment_and_closure_together(): void
    {
        Bus::fake();
        Notification::fake();
        $run = app(Runs::class)->start();
        $article = $this->article(User::factory()->create());
        $this->history()->increment($article);
        $this->history()->increment($article);
        Event::listen('eloquent.creating: '.ArticleClosureItem::class, function (): void {
            throw new RuntimeException('snapshot unavailable');
        });

        try {
            app(ProcessDeadLink::class)($article, $run->id);
            $this->fail('Snapshot persistence should fail.');
        } catch (RuntimeException $error) {
            $this->assertSame('snapshot unavailable', $error->getMessage());
        }
        $this->assertSame(ArticleStatus::Publish, $article->fresh()->status);
        $this->assertSame(2, $this->history()->get($article));
        $this->assertSame(0, ArticleClosureBatch::count());
        $this->assertSame(0, ArticleClosureItem::count());
        Bus::assertNothingDispatched();
        Notification::assertNothingSent();
    }

    public function test_sealed_run_cannot_increment_or_clear_check_history(): void
    {
        $run = app(Runs::class)->start();
        $article = $this->article(User::factory()->create());
        $this->history()->increment($article);
        app(Runs::class)->finalize($run->id, true);
        $process = app(ProcessDeadLink::class);
        $this->assertFalse($process($article, $run->id));
        $process->alive($article, $run->id);
        $this->assertSame(1, $this->history()->get($article));
        $this->assertSame(0, ArticleClosureItem::count());
    }

    public function test_exception_seals_partial_results_for_later_delivery(): void
    {
        Bus::fake();
        Notification::fake();
        $this->mock(Recover::class)->shouldReceive('__invoke')->once();
        $user = User::factory()->create();
        $first = $this->article($user);
        $second = $this->article($user);
        foreach ([$first, $second] as $article) {
            $this->history()->increment($article);
            $this->history()->increment($article);
        }
        $firstUrl = $first->contents->link;
        $this->mock(GetHeaders::class)->shouldReceive('__invoke')->andReturnUsing(function (string $url) use ($firstUrl): array {
            if ($url !== $firstUrl) {
                throw new RuntimeException('scan interrupted');
            }

            return ['HTTP/1.1 404 Not Found'];
        });
        $this->artisan('check:deadlink')->assertFailed();
        $this->assertSame(ArticleStatus::Private, $first->fresh()->status);
        $this->assertSame(ArticleStatus::Publish, $second->fresh()->status);
        $this->assertSame(ArticleClosureRunStatus::Interrupted, ArticleClosureRun::sole()->status);
        $this->assertSame($first->id, ArticleClosureItem::sole()->article_id);
        Notification::assertNothingSent();
    }

    private function article(User $user): Article
    {
        return Article::factory()->addonIntroduction()->publish()->create([
            'user_id' => $user->id,
            'contents' => ['link' => 'https://distribution.test/'.fake()->uuid(), 'description' => 'Addon'],
        ]);
    }

    private function history(): ArticleLinkCheckHistoryRepository
    {
        return app(ArticleLinkCheckHistoryRepository::class);
    }
}
