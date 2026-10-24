<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\ArticleClosure;

use App\Actions\ArticleClosure\CloseArticle;
use App\Actions\ArticleClosure\Runs;
use App\Enums\ArticleClosureReason;
use App\Enums\ArticleClosureRunStatus;
use App\Enums\ArticleStatus;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Models\ArticleClosureBatch;
use App\Models\ArticleClosureItem;
use App\Models\ArticleLinkCheckHistory;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\Feature\TestCase;

class CloseArticleTest extends TestCase
{
    public function test_closure_groups_users_and_snapshots_only_actual_transitions(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $run = app(Runs::class)->start();
        Notification::fake();
        foreach ([$user, $user, $user, $other, $other] as $owner) {
            $article = $this->article($owner);
            ArticleLinkCheckHistory::factory()->create(['article_id' => $article->id]);
            $this->assertTrue(app(CloseArticle::class)($article, $run->id, ArticleClosureReason::DeadLink));
            $this->assertSame(ArticleStatus::Private, $article->fresh()->status);
            $this->assertFalse(ArticleLinkCheckHistory::where('article_id', $article->id)->exists());
            $item = ArticleClosureItem::where('article_id', $article->id)->firstOrFail();
            $this->assertSame($article->title, $item->title);
            $this->assertSame('https://example.net/file.zip', $item->url);
            $this->assertSame(route('mypage.articles.edit', ['article' => $article->id]), $item->edit_url);
            $this->assertSame(ArticleClosureReason::DeadLink, $item->reason);
            $this->assertNotNull($item->closed_at);
            $this->assertFalse(app(CloseArticle::class)($article, $run->id, ArticleClosureReason::DeadLink));
        }
        $this->assertSame(2, ArticleClosureBatch::count());
        $this->assertSame(3, ArticleClosureBatch::where('user_id', $user->id)->firstOrFail()->items()->count());
        $this->assertSame(2, ArticleClosureBatch::where('user_id', $other->id)->firstOrFail()->items()->count());
        Notification::assertNothingSent();
    }

    public function test_same_run_deduplicates_and_new_run_records_republished_article(): void
    {
        $article = $this->article();
        $run = app(Runs::class)->start();
        $close = app(CloseArticle::class);
        $this->assertTrue($close($article, $run->id, ArticleClosureReason::DeadLink));
        $article->refresh()->update(['status' => ArticleStatus::Publish]);
        $this->assertFalse($close($article, $run->id, ArticleClosureReason::DeadLink));
        $this->assertSame(ArticleStatus::Publish, $article->fresh()->status);
        $article->refresh()->update(['user_id' => User::factory()->create()->id]);
        $this->assertFalse($close($article->fresh(), $run->id, ArticleClosureReason::DeadLink));
        $this->assertSame(1, ArticleClosureBatch::count());
        app(Runs::class)->finalize($run->id);
        $next = app(Runs::class)->start();
        $this->assertTrue($close($article, $next->id, ArticleClosureReason::DeadLink));
        $this->assertSame(2, ArticleClosureItem::where('article_id', $article->id)->count());
    }

    public function test_item_save_failure_restores_article_history_and_batch(): void
    {
        $article = $this->article();
        $before = $article->fresh()->getRawOriginal();
        $history = ArticleLinkCheckHistory::factory()->create(['article_id' => $article->id]);
        $historyBefore = $history->fresh()->getRawOriginal();
        $run = app(Runs::class)->start();
        Bus::fake();
        ArticleClosureItem::creating(function () use ($article): void {
            $this->assertSame(ArticleStatus::Private, $article->fresh()->status);
            $this->assertFalse(ArticleLinkCheckHistory::where('article_id', $article->id)->exists());
            throw new RuntimeException('cannot save snapshot');
        });
        try {
            app(CloseArticle::class)($article, $run->id, ArticleClosureReason::DeadLink);
            $this->fail('Expected snapshot failure');
        } catch (RuntimeException $exception) {
            $this->assertSame('cannot save snapshot', $exception->getMessage());
        } finally {
            ArticleClosureItem::flushEventListeners();
        }
        $this->assertSame($before, $article->fresh()->getRawOriginal());
        $this->assertSame($historyBefore, $history->fresh()->getRawOriginal());
        $this->assertSame(0, ArticleClosureBatch::count());
        $this->assertSame(0, ArticleClosureItem::count());
        Bus::assertNotDispatched(JobUpdateRelated::class);
    }

    public function test_stale_url_owner_type_status_deleted_and_opt_out_are_rejected(): void
    {
        $run = app(Runs::class)->start();
        foreach (['url', 'owner', 'type', 'status', 'deleted', 'optout', 'retired', 'ignored'] as $scenario) {
            $article = $this->article();
            $latest = Article::findOrFail($article->id);
            match ($scenario) {
                'url' => $latest->update(['contents' => ['link' => 'https://new.example.net/file.zip']]),
                'owner' => $latest->update(['user_id' => User::factory()->create()->id]),
                'type' => $latest->update(['post_type' => 'addon-post', 'contents' => ['file' => 1]]),
                'status' => $latest->update(['status' => ArticleStatus::Private]),
                'deleted' => $latest->delete(),
                'optout' => $latest->update(['contents' => ['link' => $article->contents->link, 'exclude_link_check' => true]]),
                'retired' => $latest->user->delete(),
                'ignored' => $latest->update(['contents' => ['link' => 'https://getuploader.com/a']]),
            };
            $this->assertFalse(app(CloseArticle::class)($article, $run->id, ArticleClosureReason::DeadLink), $scenario);
        }
        $this->assertSame(0, ArticleClosureItem::count());
    }

    public function test_service_closed_bypasses_domain_and_flag_but_still_rechecks_url(): void
    {
        $article = $this->article();
        $article->update(['contents' => ['link' => 'https://getuploader.com/a', 'exclude_link_check' => true]]);
        $expected = $article->fresh();
        $run = app(Runs::class)->start(ArticleClosureReason::ServiceClosed);
        $this->assertFalse(app(CloseArticle::class)($expected, $run->id, ArticleClosureReason::DeadLink));
        $this->assertTrue(app(CloseArticle::class)($expected, $run->id, ArticleClosureReason::ServiceClosed));
        $this->assertSame(ArticleClosureReason::ServiceClosed, ArticleClosureItem::firstOrFail()->reason);
        $article->update(['status' => ArticleStatus::Publish, 'contents' => ['link' => 'https://new.example.net/']]);
        $next = app(Runs::class)->start(ArticleClosureReason::ServiceClosed);
        $this->assertFalse(app(CloseArticle::class)($expected, $next->id, ArticleClosureReason::ServiceClosed));
    }

    public function test_matching_ignored_domain_is_ineligible_without_service_override(): void
    {
        $article = $this->article();
        $article->update(['contents' => ['link' => 'https://getuploader.com/a', 'exclude_link_check' => false]]);
        $run = app(Runs::class)->start();
        $this->assertFalse(app(CloseArticle::class)($article, $run->id, ArticleClosureReason::DeadLink));
        $this->assertSame(ArticleStatus::Publish, $article->fresh()->status);
        $this->assertTrue(app(CloseArticle::class)($article, $run->id, ArticleClosureReason::ServiceClosed));
    }

    public function test_run_heartbeat_and_finalization_are_irreversible_and_stop_late_writes(): void
    {
        $runs = app(Runs::class);
        $run = $runs->start();
        $this->assertInstanceOf(CarbonImmutable::class, $run->started_at);
        $this->travel(1)->minutes();
        $this->assertTrue($runs->heartbeat($run->id));
        $this->assertTrue($run->fresh()->heartbeat_at->greaterThan($run->heartbeat_at));
        $runs->finalize($run->id, true);
        $finalized = $run->fresh()->getRawOriginal();
        $this->assertSame(ArticleClosureRunStatus::Interrupted, $run->fresh()->status);
        $runs->finalize($run->id);
        $this->assertFalse($runs->heartbeat($run->id));
        $this->assertSame($finalized, $run->fresh()->getRawOriginal());
        $this->assertFalse(app(CloseArticle::class)($this->article(), $run->id, ArticleClosureReason::DeadLink));
        $this->assertSame(0, ArticleClosureBatch::count());
    }

    public function test_snapshot_survives_article_edit_and_hard_delete_of_article_and_user(): void
    {
        $article = $this->article();
        $run = app(Runs::class)->start();
        $this->assertTrue(app(CloseArticle::class)($article, $run->id, ArticleClosureReason::DeadLink));
        $item = ArticleClosureItem::firstOrFail();
        $before = $item->getRawOriginal();
        $article->update(['title' => 'changed', 'contents' => ['link' => 'https://new.example.net']]);
        $owner = $article->user;
        $article->forceDelete();
        $owner->forceDelete();
        $this->assertSame($before, $item->fresh()->getRawOriginal());
        $this->assertSame(1, ArticleClosureBatch::count());
    }

    private function article(?User $user = null): Article
    {
        return Article::factory()->addonIntroduction()->publish()->create([
            'user_id' => ($user ?? User::factory()->create())->id,
            'contents' => ['link' => 'https://example.net/file.zip', 'description' => 'addon'],
        ]);
    }
}
