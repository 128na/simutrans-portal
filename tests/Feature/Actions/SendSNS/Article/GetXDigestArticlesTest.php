<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\SendSNS\Article;

use App\Actions\SendSNS\Article\Data\XDigestArticle;
use App\Actions\SendSNS\Article\GetXDigestArticles;
use App\Enums\XDigestArticleType;
use App\Events\Article\ArticleUpdated;
use App\Listeners\Article\OnArticleUpdated;
use App\Models\Article;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\TestCase;

class GetXDigestArticlesTest extends TestCase
{
    public function test_merges_new_and_updated_articles_by_event_time_descending_and_caps_at_three(): void
    {
        $cutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        $until = CarbonImmutable::parse('2026-09-16 23:59:59');

        // イベント時刻の並び: update(10:00) -> new(11:00) -> update(12:00) -> new(13:00)
        // 型グループ順(新規優先)なら new,new,update,updateになってしまうが、
        // ADR-0005が採用するイベント時刻マージでは 13:00(new), 12:00(update), 11:00(new), 10:00(update) の順になる。
        $updateEarly = Article::factory()->publish()->create([
            'sns_digest_published_at' => $cutoff->subDays(5),
            'sns_digest_updated_at' => $cutoff->addHours(10),
        ]);
        $newEarly = Article::factory()->publish()->create([
            'sns_digest_published_at' => $cutoff->addHours(11),
            'sns_digest_updated_at' => null,
        ]);
        $updateLate = Article::factory()->publish()->create([
            'sns_digest_published_at' => $cutoff->subDays(3),
            'sns_digest_updated_at' => $cutoff->addHours(12),
        ]);
        $newLate = Article::factory()->publish()->create([
            'sns_digest_published_at' => $cutoff->addHours(13),
            'sns_digest_updated_at' => null,
        ]);

        $action = app(GetXDigestArticles::class);
        $result = $action($cutoff, $until);

        $ids = $result->items->map(fn (XDigestArticle $item): int => $item->article->id)->all();

        // 上位3件はイベント時刻降順。4件目(updateEarly)は掲載されないがtotalCountには含まれる。
        $this->assertSame([$newLate->id, $updateLate->id, $newEarly->id], $ids);
        $this->assertSame(4, $result->totalCount);
    }

    public function test_classifies_as_publish_when_published_after_cutoff_otherwise_update(): void
    {
        $cutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        $until = CarbonImmutable::parse('2026-09-17 00:00:00');

        $new = Article::factory()->publish()->create([
            'sns_digest_published_at' => $cutoff->addMinute(),
            'sns_digest_updated_at' => null,
        ]);
        $update = Article::factory()->publish()->create([
            'sns_digest_published_at' => $cutoff->subDay(),
            'sns_digest_updated_at' => $cutoff->addMinute(),
        ]);

        $action = app(GetXDigestArticles::class);
        $result = $action($cutoff, $until);

        $types = $result->items->mapWithKeys(fn (XDigestArticle $item): array => [$item->article->id => $item->type])->all();

        $this->assertSame(XDigestArticleType::Publish, $types[$new->id]);
        $this->assertSame(XDigestArticleType::Update, $types[$update->id]);
    }

    public function test_excludes_articles_exactly_at_the_cutoff_boundary(): void
    {
        $cutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        $until = CarbonImmutable::parse('2026-09-17 00:00:00');

        // sns_digest_published_at/sns_digest_updated_atがcutoffと厳密に等しい場合は対象外(cutoffより「後」が条件)。
        Article::factory()->publish()->create([
            'sns_digest_published_at' => $cutoff,
            'sns_digest_updated_at' => $cutoff,
        ]);

        $action = app(GetXDigestArticles::class);
        $result = $action($cutoff, $until);

        $this->assertSame(0, $result->totalCount);
        $this->assertTrue($result->items->isEmpty());
    }

    public function test_excludes_non_publish_status_and_events_outside_the_window(): void
    {
        $cutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        $until = CarbonImmutable::parse('2026-09-16 12:00:00');

        // 下書きは対象外
        Article::factory()->draft()->create([
            'sns_digest_published_at' => $cutoff->addHour(),
            'sns_digest_updated_at' => null,
        ]);

        // untilより後のイベントは対象外
        Article::factory()->publish()->create([
            'sns_digest_published_at' => $until->addHour(),
            'sns_digest_updated_at' => null,
        ]);

        // cutoff以前のイベントは対象外
        Article::factory()->publish()->create([
            'sns_digest_published_at' => $cutoff->subHour(),
            'sns_digest_updated_at' => null,
        ]);

        $action = app(GetXDigestArticles::class);
        $result = $action($cutoff, $until);

        $this->assertSame(0, $result->totalCount);
        $this->assertTrue($result->items->isEmpty());
    }

    public function test_total_count_equals_items_count_when_three_or_fewer_matches(): void
    {
        $cutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        $until = CarbonImmutable::parse('2026-09-16 23:59:59');

        Article::factory()->publish()->create([
            'sns_digest_published_at' => $cutoff->addHour(),
            'sns_digest_updated_at' => null,
        ]);
        Article::factory()->publish()->create([
            'sns_digest_published_at' => $cutoff->addHours(2),
            'sns_digest_updated_at' => null,
        ]);

        $action = app(GetXDigestArticles::class);
        $result = $action($cutoff, $until);

        $this->assertSame(2, $result->totalCount);
        $this->assertCount(2, $result->items);
    }

    public function test_excludes_article_whose_modified_at_was_bumped_but_sns_digest_updated_at_was_not_stamped(): void
    {
        $cutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        $until = CarbonImmutable::parse('2026-09-17 00:00:00');

        // shouldNotify=false の編集や、ステータスのみのAPI/MCP更新を模した状態:
        // modified_atはウィンドウ内で更新されているが、通知確定を意味するsns_digest_updated_atはスタンプされていない。
        Article::factory()->publish()->create([
            'published_at' => $cutoff->subDays(5),
            'modified_at' => $cutoff->addHour(),
            'sns_digest_published_at' => $cutoff->subDays(5),
            'sns_digest_updated_at' => null,
        ]);

        $action = app(GetXDigestArticles::class);
        $result = $action($cutoff, $until);

        $this->assertSame(0, $result->totalCount);
        $this->assertTrue($result->items->isEmpty());
    }

    public function test_article_updated_via_real_listener_with_should_notify_false_never_gets_stamped_and_is_excluded(): void
    {
        Notification::fake();

        $cutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        $now = $cutoff->addHour();
        $this->travelTo($now);

        $article = Article::factory()->publish()->create([
            'published_at' => $cutoff->subDays(5),
            'modified_at' => $cutoff->subDays(5),
            'sns_digest_published_at' => $cutoff->subDays(5),
            'sns_digest_updated_at' => null,
        ]);

        // shouldNotify=false: ArticleStatusController/UserArticleUpdateStatusTool と同様、通知を希望しない更新。
        app(OnArticleUpdated::class)->handle(new ArticleUpdated($article, false, false));

        $article->refresh();
        $this->assertNull($article->sns_digest_updated_at);

        $until = CarbonImmutable::now();
        $action = app(GetXDigestArticles::class);
        $result = $action($cutoff, $until);

        $this->assertSame(0, $result->totalCount);
    }

    public function test_not_yet_published_branch_stamps_sns_digest_published_at_not_updated_at(): void
    {
        Notification::fake();

        $cutoff = CarbonImmutable::parse('2026-09-16 00:00:00');
        $now = $cutoff->addHour();
        $this->travelTo($now);

        $article = Article::factory()->publish()->create([
            'published_at' => null,
            'modified_at' => null,
            'sns_digest_published_at' => null,
            'sns_digest_updated_at' => null,
        ]);

        // notYetPublished=true かつ shouldNotify=true: 初めての公開扱い(SendArticlePublished)。
        app(OnArticleUpdated::class)->handle(new ArticleUpdated($article, true, true));

        $article->refresh();
        $this->assertNotNull($article->sns_digest_published_at);
        $this->assertNull($article->sns_digest_updated_at);

        $until = CarbonImmutable::now();
        $action = app(GetXDigestArticles::class);
        $result = $action($cutoff, $until);

        $this->assertSame(1, $result->totalCount);
        $this->assertSame(XDigestArticleType::Publish, $result->items->first()->type);
    }
}
