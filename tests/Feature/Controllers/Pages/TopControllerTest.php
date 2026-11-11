<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\File;
use Illuminate\Testing\TestResponse;
use Tests\Feature\TestCase;

class TopControllerTest extends TestCase
{
    public function test_top(): void
    {
        $testResponse = $this->get(route('index'));

        $testResponse->assertOk();
    }

    public function test_top_shows_the_eight_latest_unique_cards_and_both_dates_for_updates(): void
    {
        $pak64 = Category::where('type', CategoryType::Pak)->where('slug', '64')->sole();
        $pak128 = Category::where('type', CategoryType::Pak)->where('slug', '128')->sole();
        $recent = now()->subDay();
        $ids = [];
        for ($i = 0; $i < 9; $i++) {
            $article = $this->createAddonIntroduction();
            $article->update([
                'modified_at' => $recent->subDays($i),
                'published_at' => $recent->subDays($i + 1),
            ]);
            $article->categories()->attach(match ($i) {
                0 => [$pak64->id, $pak128->id],
                2 => [$pak128->id],
                default => [$pak64->id],
            });
            $ids[] = $article->id;
        }
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $response = $this->get(route('index'))
            ->assertOk()
            ->assertViewHas('latestCards', fn (array $cards): bool => array_column($cards, 'id') === array_slice($ids, 0, 8))
            ->assertSee('新着アドオンをもっと見る');
        $cards = $this->embeddedLatestCards((string) $response->getContent(), 'top');
        $this->assertSame(array_slice($ids, 0, 8), array_column($cards, 'id'));
        // 更新は更新日と公開日の両方を一覧のカードと同じ形式で渡す（見出しに更新日、カード下に公開日）
        $this->assertSame(
            ['published_at' => $recent->subDay()->format('Y/m/d H:i'), 'modified_at' => $recent->format('Y/m/d H:i'), 'is_updated' => true],
            array_intersect_key($cards[0], array_flip(['is_updated', 'modified_at', 'published_at'])),
        );

        $this->get(route('latest'))->assertOk()
            ->assertSee('href="#pak64"', false)
            ->assertSee('id="pak64"', false)
            ->assertSee(route('pak.others'));
    }

    public function test_cards_show_new_or_updated_in_the_heading_row_and_same_day_edits_stay_new(): void
    {
        $pak64 = Category::where('type', CategoryType::Pak)->where('slug', '64')->sole();
        $published = now()->subDays(3)->setTime(10, 0);
        $updated = $this->createAddonIntroduction();
        $updated->update(['title' => '更新された記事', 'published_at' => $published, 'modified_at' => $published->addDay()]);
        $sameDay = $this->createAddonIntroduction();
        $sameDay->update(['title' => '公開直後に直した記事', 'published_at' => $published, 'modified_at' => $published->addSeconds(5)]);
        $acrossMidnight = $this->createAddonIntroduction();
        $acrossMidnight->update(['title' => '日付をまたいで公開した記事', 'published_at' => $published->setTime(23, 59, 58), 'modified_at' => $published->addDay()->setTime(0, 0, 3)]);
        $updated->categories()->attach($pak64);
        $sameDay->categories()->attach($pak64);
        $acrossMidnight->categories()->attach($pak64);
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $cards = collect($this->embeddedLatestCards((string) $this->get(route('index'))->assertOk()->getContent(), 'top'));

        // 「新規／更新」は公開日より後の日に、公開から 60 秒より後に更新されたものだけを「更新」にする（描画はフロントのテストで守る）
        $this->assertTrue($cards->firstWhere('id', $updated->id)['is_updated']);
        $this->assertFalse($cards->firstWhere('id', $sameDay->id)['is_updated']);
        $this->assertFalse($cards->firstWhere('id', $acrossMidnight->id)['is_updated']);
    }

    /**
     * 新着カードは一覧ページのカード（ArticleCard リソース）と同じ項目・同じ値にする。
     * 入手の操作は一覧と同じ CV 経路（ダウンロード／掲載先）を使う
     */
    public function test_latest_cards_match_list_cards(): void
    {
        $post = $this->attachCategories($this->createAddonPost(), ['128-japan', 'any'], ['trains']);
        $intro = $this->attachCategories($this->createAddonIntroduction(), ['64'], ['rail-tools', 'buildings']);
        $nickless = $this->attachCategories($this->createAddonIntroduction(), ['128'], []);
        $nickless->user->update(['nickname' => null]);
        $this->assertNotSame($post->id, $intro->id);
        // タグは id 順ではなく表示順（order）で並べる。id 順と食い違うよう、先に登録された「Pak問わず」を後ろへ回す
        Category::where('type', CategoryType::Pak)->where('slug', 'any')->update(['order' => 15000]);
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $top = collect($this->embeddedLatestCards((string) $this->get(route('index'))->assertOk()->getContent(), 'top'))->keyBy('id');
        $latestHtml = (string) $this->get(route('latest'))->assertOk()->getContent();
        $latest = collect([
            ...$this->embeddedLatestCards($latestHtml, 'pak128-japan'),
            ...$this->embeddedLatestCards($latestHtml, 'pak128'),
            ...$this->embeddedLatestCards($latestHtml, 'pak64'),
        ])->keyBy('id');

        $list = collect([route('pak.128japan'), route('pak.128'), route('pak.64')])
            ->flatMap(fn (string $url): array => $this->listCardsOf($this->get($url)))
            ->keyBy('id');

        foreach ([$post, $intro, $nickless] as $article) {
            $expected = $this->onlyPakAndAddon($list[$article->id]);
            $this->assertSame($expected, $this->withoutLatestOnly($top[$article->id]));
            $this->assertSame($expected, $this->withoutLatestOnly($latest[$article->id]));
        }
        $this->assertSame(['128-japan', 'any', 'trains'], array_column($top[$post->id]['categories'], 'slug'));
        $this->assertSame(route('articles.download', ['article' => $post->id]), $top[$post->id]['download_url']);
        $this->assertArrayNotHasKey('addon_page_url', $top[$post->id]);
        $this->assertSame(route('articles.conversion', ['article' => $intro->id]), $top[$intro->id]['addon_page_url']);
        $this->assertArrayNotHasKey('download_url', $top[$intro->id]);
        $this->assertSame(route('users.show', ['userIdOrNickname' => $nickless->user_id]), $top[$nickless->id]['user']['url']);
    }

    /**
     * 旧コードへ戻しても pak のタグを出せるよう、保存データには pak の slug（paks）も表示順で残す
     */
    public function test_snapshot_keeps_pak_slugs_for_older_code(): void
    {
        $multi = $this->attachCategories($this->createAddonIntroduction(), ['256', '64', 'any'], ['trains']);
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $latest = json_decode((string) File::get(config()->string('public-article-snapshot.path')), true)['latest'];
        $card = collect($latest['64'])->firstWhere('id', $multi->id);
        $this->assertSame(['any', '64', '256'], $card['paks']);
        $this->assertSame(['any', '64', '256', 'trains'], array_column($card['categories'], 'slug'));
    }

    /**
     * 項目を追加する前の保存データ（version 2 で post_type・categories・user・paks が無い、または壊れている）でも、
     * 新着は出して入手の操作・タグ・作者だけを省く
     */
    public function test_older_or_broken_card_fields_keep_latest_and_hide_only_those_parts(): void
    {
        $article = $this->attachCategories($this->createAddonPost(), ['64'], ['trains']);
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();
        $path = config()->string('public-article-snapshot.path');
        $snapshot = json_decode((string) File::get($path), true);

        foreach ([
            null,
            ['post_type' => 'page', 'categories' => 'Pak64', 'user' => 'alice', 'paks' => 'Pak64'],
            ['post_type' => null, 'categories' => [['id' => '1', 'type' => 'pak', 'slug' => '64']], 'user' => ['name' => 1, 'nickname' => null], 'paks' => [64]],
            ['post_type' => 1, 'categories' => [['id' => 1, 'type' => 'license', 'slug' => 'cc-by']], 'user' => ['name' => 'alice'], 'paks' => ['64' => '64']],
        ] as $fields) {
            $broken = $snapshot;
            $broken['latest']['64'] = array_map(function (array $card) use ($fields): array {
                unset($card['post_type'], $card['categories'], $card['user'], $card['paks']);

                return $fields === null ? $card : [...$card, ...$fields];
            }, $broken['latest']['64']);
            File::put($path, json_encode($broken));

            $top = $this->embeddedLatestCards((string) $this->get(route('index'))->assertOk()->getContent(), 'top');
            $latest = $this->embeddedLatestCards((string) $this->get(route('latest'))->assertOk()->getContent(), 'pak64');
            foreach ([$top, $latest] as $cards) {
                $this->assertSame([$article->id], array_column($cards, 'id'));
                $this->assertSame($article->title, $cards[0]['title']);
                $this->assertNull($cards[0]['post_type']);
                $this->assertSame([], $cards[0]['categories']);
                $this->assertNull($cards[0]['user']);
                $this->assertArrayNotHasKey('download_url', $cards[0]);
                $this->assertArrayNotHasKey('addon_page_url', $cards[0]);
            }
        }
    }

    public function test_お知らせ一覧のリンクは日本語スラッグでも二重エンコードされない(): void
    {
        $article = $this->createAnnounce();
        $article->update([
            'title' => '日本語のお知らせ記事',
            'slug' => '日本語のお知らせ記事',
        ]);

        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();
        $testResponse = $this->get(route('index'));

        $testResponse->assertOk();
        $html = (string) $testResponse->getContent();
        $this->assertStringNotContainsString('%25', $html);
        $this->assertStringContainsString(e($article->fresh()?->showUrl()), $html);
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
     * 一覧ページ（pak 別一覧）に渡したカードを、ブラウザが受け取る JSON の形にする
     *
     * @return list<array<string, mixed>>
     */
    private function listCardsOf(TestResponse $response): array
    {
        $response->assertOk();
        /** @var AnonymousResourceCollection $articles */
        $articles = $response->viewData('articles');

        return array_values(array_map(
            fn (JsonResource $card): array => json_decode(json_encode($card, JSON_THROW_ON_ERROR), true),
            $articles->collection->all(),
        ));
    }

    /**
     * 一覧のカードもタグは pak と種類だけを出すので、その 2 種に絞る（並び順はそのまま比べる）
     *
     * @param  array<string, mixed>  $card
     * @return array<string, mixed>
     */
    private function onlyPakAndAddon(array $card): array
    {
        $card['categories'] = array_values(array_filter(
            $card['categories'],
            fn (array $category): bool => in_array($category['type'], ['pak', 'addon'], true),
        ));
        ksort($card);

        return $card;
    }

    /**
     * 新着だけが持つ項目（is_updated）を外す
     *
     * @param  array<string, mixed>  $card
     * @return array<string, mixed>
     */
    private function withoutLatestOnly(array $card): array
    {
        unset($card['is_updated']);
        ksort($card);

        return $card;
    }
}
