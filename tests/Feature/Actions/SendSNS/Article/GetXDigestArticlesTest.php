<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\SendSNS\Article;

use App\Actions\SendSNS\Article\Data\XDigestArticle;
use App\Actions\SendSNS\Article\GetXDigestArticles;
use App\Enums\XDigestArticleType;
use App\Models\Article;
use Carbon\CarbonImmutable;
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
            'published_at' => $cutoff->subDays(5),
            'modified_at' => $cutoff->addHours(10),
        ]);
        $newEarly = Article::factory()->publish()->create([
            'published_at' => $cutoff->addHours(11),
            'modified_at' => $cutoff->addHours(11),
        ]);
        $updateLate = Article::factory()->publish()->create([
            'published_at' => $cutoff->subDays(3),
            'modified_at' => $cutoff->addHours(12),
        ]);
        $newLate = Article::factory()->publish()->create([
            'published_at' => $cutoff->addHours(13),
            'modified_at' => $cutoff->addHours(13),
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
            'published_at' => $cutoff->addMinute(),
            'modified_at' => $cutoff->addMinute(),
        ]);
        $update = Article::factory()->publish()->create([
            'published_at' => $cutoff->subDay(),
            'modified_at' => $cutoff->addMinute(),
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

        // published_at/modified_atがcutoffと厳密に等しい場合は対象外(cutoffより「後」が条件)。
        Article::factory()->publish()->create([
            'published_at' => $cutoff,
            'modified_at' => $cutoff,
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
            'published_at' => $cutoff->addHour(),
            'modified_at' => $cutoff->addHour(),
        ]);

        // untilより後のイベントは対象外
        Article::factory()->publish()->create([
            'published_at' => $until->addHour(),
            'modified_at' => $until->addHour(),
        ]);

        // cutoff以前のイベントは対象外
        Article::factory()->publish()->create([
            'published_at' => $cutoff->subHour(),
            'modified_at' => $cutoff->subHour(),
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
            'published_at' => $cutoff->addHour(),
            'modified_at' => $cutoff->addHour(),
        ]);
        Article::factory()->publish()->create([
            'published_at' => $cutoff->addHours(2),
            'modified_at' => $cutoff->addHours(2),
        ]);

        $action = app(GetXDigestArticles::class);
        $result = $action($cutoff, $until);

        $this->assertSame(2, $result->totalCount);
        $this->assertCount(2, $result->items);
    }
}
