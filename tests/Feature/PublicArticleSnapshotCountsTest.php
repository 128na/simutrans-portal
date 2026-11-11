<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\Front\PublicArticleSnapshot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * pak × 種類の公開記事数は表示データの生成時に集計し、トップと pak 別一覧は保存済みの件数を読む
 */
class PublicArticleSnapshotCountsTest extends TestCase
{
    public function test_generation_counts_only_public_addon_articles_per_main_pak_and_addon(): void
    {
        $this->createCountFixtures();

        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $this->assertSame([
            '128-japan' => [
                'rail-tools' => ['total' => 1, 'addon_post' => 1],
                'road-vehicles' => ['total' => 2, 'addon_post' => 1],
                'trains' => ['total' => 3, 'addon_post' => 1],
            ],
            '128' => [],
            '64' => [
                'road-vehicles' => ['total' => 1, 'addon_post' => 1],
                'trains' => ['total' => 1, 'addon_post' => 0],
            ],
        ], $this->sortedCounts(app(PublicArticleSnapshot::class)->read()['pak_addon_counts']));
    }

    /**
     * 生成後に増えた記事は次の生成まで件数に入らない（閲覧時に集計していない）
     */
    public function test_top_and_pak_list_show_saved_counts_without_counting_on_view(): void
    {
        $this->createCountFixtures();
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();
        $this->createArticleIn(['128-japan'], ['trains']);

        DB::enableQueryLog();
        DB::flushQueryLog();
        try {
            $top = $this->get(route('index'));
            $this->assertSame([], DB::getQueryLog());
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }

        $top->assertOk()
            ->assertSeeInOrder([
                route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'trains']).'" class="v2-chip">', '鉄道車両', '>3<span class="sr-only">件</span>',
                route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'road-vehicles']).'" class="v2-chip">', '自動車', '>2<span class="sr-only">件</span>',
                route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'rail-tools']).'" class="v2-chip">', '鉄道施設', '>1<span class="sr-only">件</span>',
                route('categories.pakAddon', ['pak' => '64', 'addon' => 'road-vehicles']).'" class="v2-chip">', '自動車', '>1<span class="sr-only">件</span>',
            ], false)
            ->assertDontSee(route('categories.pakAddon', ['pak' => '128', 'addon' => 'trains']), false);

        $this->get(route('pak.128japan'))->assertOk()
            ->assertViewHas('addonCounts', ['rail-tools' => 1, 'road-vehicles' => 2, 'trains' => 3])
            ->assertSeeInOrder(['鉄道車両', '3<span class="sr-only">件</span>'], false);
        $this->get(route('pak.128japan', ['postTypes' => ['addon-post']]))->assertOk()
            ->assertViewHas('addonCounts', ['rail-tools' => 1, 'road-vehicles' => 1, 'trains' => 1]);
        $this->get(route('pak.64'))->assertOk()
            ->assertViewHas('addonCounts', ['road-vehicles' => 1, 'trains' => 1]);
        $this->get(route('pak.others'))->assertOk()
            ->assertDontSee('<span class="sr-only">件</span>', false);
    }

    /**
     * 非公開・削除済み・退会ユーザーの記事は保存側（生成時の集計）で除かれているため、件数に入らない
     */
    public function test_categories_page_shows_saved_counts_without_querying_on_view(): void
    {
        $this->createCountFixtures();
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();
        $this->createArticleIn(['128-japan'], ['trains']);

        DB::enableQueryLog();
        DB::flushQueryLog();
        try {
            $response = $this->get(route('categories.index'));
            $this->assertSame([], DB::getQueryLog());
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }

        $response->assertOk()
            ->assertSeeInOrder([
                'Pak128.Japan',
                route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'trains']), '鉄道車両 (3)',
                route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'rail-tools']), '鉄道施設 (1)',
                route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'road-vehicles']), '自動車 (2)',
                'Pak128',
                'Pak64',
                route('categories.pakAddon', ['pak' => '64', 'addon' => 'trains']), '鉄道車両 (1)',
                route('categories.pakAddon', ['pak' => '64', 'addon' => 'road-vehicles']), '自動車 (1)',
            ], false)
            ->assertDontSee(route('categories.pakAddon', ['pak' => '128', 'addon' => 'trains']), false)
            ->assertDontSee(route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'ships']), false)
            ->assertDontSee('Pak256');
    }

    public function test_categories_page_shows_links_without_counts_when_saved_data_is_unusable(): void
    {
        $this->createArticleIn(['128-japan'], ['trains']);
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();
        $path = config()->string('public-article-snapshot.path');
        $snapshot = json_decode((string) File::get($path), true);

        foreach ([
            ['version' => 1, 'pak_addon_counts' => null],
            ['pak_addon_counts' => ['128-japan' => ['trains' => ['total' => '3', 'addon_post' => 1]], '128' => [], '64' => []]],
            ['generated_at' => now()->subDays(2)->getTimestamp()],
        ] as $override) {
            File::put($path, json_encode(array_filter([...$snapshot, ...$override], fn ($value): bool => $value !== null)));

            $this->get(route('categories.index'))->assertOk()
                ->assertSee(route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'trains']), false)
                ->assertSee(route('categories.pakAddon', ['pak' => '64', 'addon' => 'ships']), false)
                ->assertSee('鉄道車両')
                ->assertDontSee('鉄道車両 (', false)
                ->assertDontSee('未指定');
        }

        File::delete($path);
        $this->get(route('categories.index'))->assertOk()
            ->assertSee(route('categories.pakAddon', ['pak' => '128', 'addon' => 'trains']), false)
            ->assertDontSee('鉄道車両 (', false);
    }

    public function test_top_shows_latest_and_counts_while_the_database_is_unavailable(): void
    {
        $this->createCountFixtures();
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $default = config('database.default');
        config(['database.connections.unavailable' => [...config('database.connections.'.$default), 'host' => '127.0.0.1', 'port' => '1', 'url' => null]]);
        config(['database.default' => 'unavailable']);
        try {
            $this->get(route('index'))->assertOk()
                ->assertSee('新着アドオン')
                ->assertSee(route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'trains']), false)
                ->assertSee('3<span class="sr-only">件</span>', false);
        } finally {
            config(['database.default' => $default]);
            DB::purge('unavailable');
        }
    }

    public function test_old_format_and_broken_counts_keep_latest_and_hide_counts(): void
    {
        $article = $this->createArticleIn(['128-japan'], ['trains']);
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();
        $path = config()->string('public-article-snapshot.path');
        $snapshot = json_decode((string) File::get($path), true);

        foreach ([
            ['version' => 1, 'pak_addon_counts' => null],
            ['pak_addon_counts' => ['128-japan' => ['trains' => ['total' => '3', 'addon_post' => 1]], '128' => [], '64' => []]],
            ['pak_addon_counts' => ['128-japan' => []]],
        ] as $override) {
            File::put($path, json_encode(array_filter([...$snapshot, ...$override], fn ($value): bool => $value !== null)));

            $this->get(route('index'))->assertOk()
                ->assertSee($article->title)
                ->assertSee(route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'trains']), false)
                ->assertDontSee('<span class="sr-only">件</span>', false);
            $this->get(route('pak.128japan'))->assertOk()->assertViewHas('addonCounts', null);
        }
    }

    public function test_top_switches_latest_cards_by_pak(): void
    {
        $pak64 = $this->createArticleIn(['64'], ['trains']);
        $pak128 = $this->createArticleIn(['128'], ['trains']);
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $this->get(route('index'))->assertOk()
            ->assertSee($pak64->title)->assertSee($pak128->title)
            ->assertSee('href="'.route('latest').'"', false);

        $this->get(route('index', ['pak' => '64']))->assertOk()
            ->assertSee($pak64->title)->assertDontSee($pak128->title)
            ->assertSee('href="'.route('pak.64').'"', false)
            ->assertSeeInOrder(['aria-current="true"', 'Pak64'], false);

        $this->get(route('index', ['pak' => 'unknown']))->assertOk()
            ->assertViewHas('selectedPak', null)
            ->assertSee($pak64->title)->assertSee($pak128->title);
    }

    /**
     * 件数が組み合わせごとに違うように作り、公開中のアドオン記事以外も混ぜる。
     * 主要3 pak 以外（256）と、下書き・削除済み・退会したユーザー・一般記事は数えない
     */
    private function createCountFixtures(): void
    {
        $this->createArticleIn(['128-japan'], ['trains']);
        $this->createArticleIn(['128-japan'], ['trains']);
        $this->createArticleIn(['128-japan'], ['trains', 'rail-tools'], addonPost: true);
        $this->createArticleIn(['128-japan', '64'], ['road-vehicles'], addonPost: true);
        $this->createArticleIn(['128-japan'], ['road-vehicles']);
        $this->createArticleIn(['64'], ['trains']);
        $this->createArticleIn(['256'], ['trains']);
        $this->attachCategories(Article::factory()->addonIntroduction()->draft()->create(['user_id' => User::factory()->create()->id]), ['128-japan'], ['trains']);
        $this->createArticleIn(['128-japan'], ['trains'])->delete();
        $this->createArticleIn(['128-japan'], ['trains'])->user->delete();
        $this->attachCategories($this->createPage(), ['128-japan'], ['trains']);
    }

    /**
     * @param  list<string>  $paks
     * @param  list<string>  $addons
     */
    private function createArticleIn(array $paks, array $addons, bool $addonPost = false): Article
    {
        return $this->attachCategories($addonPost ? $this->createAddonPost() : $this->createAddonIntroduction(), $paks, $addons);
    }

    /**
     * @param  list<string>  $paks
     * @param  list<string>  $addons
     */
    private function attachCategories(Article $article, array $paks, array $addons): Article
    {
        $categories = Category::query()
            ->where(fn ($query) => $query->where('type', CategoryType::Pak)->whereIn('slug', $paks))
            ->orWhere(fn ($query) => $query->where('type', CategoryType::Addon)->whereIn('slug', $addons))
            ->pluck('id');
        $this->assertCount(count($paks) + count($addons), $categories);
        $article->categories()->attach($categories);

        return $article;
    }

    /**
     * @param  array<string, array<string, array{total: int, addon_post: int}>>|null  $counts
     * @return array<string, array<string, array{total: int, addon_post: int}>>|null
     */
    private function sortedCounts(?array $counts): ?array
    {
        if ($counts === null) {
            return null;
        }

        return array_map(function (array $addons): array {
            ksort($addons);

            return $addons;
        }, $counts);
    }
}
