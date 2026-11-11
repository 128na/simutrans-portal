<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\File;
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

        $this->get(route('index'))
            ->assertOk()
            ->assertViewHas('latestCards', fn ($cards): bool => $cards->pluck('id')->values()->all() === array_slice($ids, 0, 8))
            ->assertSee('新着アドオンをもっと見る')
            ->assertSee('更新 <time datetime="'.$recent->toIso8601String().'">'.$recent->toDateString().'</time>', false)
            ->assertSee('公開 <time datetime="'.$recent->subDay()->toIso8601String().'">'.$recent->subDay()->toDateString().'</time>', false);

        $this->get(route('latest'))->assertOk()
            ->assertSee('href="#pak64"', false)
            ->assertSee('id="pak64"', false)
            ->assertSee(route('pak.others'));
    }

    public function test_cards_show_a_new_or_updated_badge_and_same_day_edits_stay_new(): void
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

        $html = (string) $this->get(route('index'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/aria-hidden="true"[^>]*>更新<\/span>(?:(?!<\/a>).)*更新された記事/s', $html);
        $this->assertMatchesRegularExpression('/aria-hidden="true"[^>]*>新規<\/span>(?:(?!<\/a>).)*公開直後に直した記事/s', $html);
        $this->assertMatchesRegularExpression('/aria-hidden="true"[^>]*>新規<\/span>(?:(?!<\/a>).)*日付をまたいで公開した記事/s', $html);
        // 新規は日付が1つだけなので、日付の行の「新規」はバッジと重ねず読み上げ用にだけ出す
        $this->assertMatchesRegularExpression('/公開直後に直した記事(?:(?!<\/a>).)*<span class="sr-only">新規<\/span><time/s', $html);
        $this->assertMatchesRegularExpression('/更新された記事(?:(?!<\/a>).)*更新 <time(?:(?!<\/a>).)*公開 <time/s', $html);
    }

    /**
     * 「すべて」表示でも各カードの pak が分かるように、生成時に保存した pak を一覧のカードと同じ色分けのタグで出す
     */
    public function test_cards_show_pak_tags_saved_at_generation(): void
    {
        $multi = $this->createArticleInPaks('64 と 256 の記事', ['256', '64', 'any']);
        $japan = $this->createArticleInPaks('128-japan の記事', ['128-japan']);
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $latest = json_decode((string) File::get(config()->string('public-article-snapshot.path')), true)['latest'];
        $this->assertSame(['any', '64', '256'], collect($latest['64'])->firstWhere('id', $multi->id)['paks']);
        $this->assertSame(['128-japan'], collect($latest['128-japan'])->firstWhere('id', $japan->id)['paks']);

        $html = (string) $this->get(route('index'))->assertOk()->getContent();
        $this->assertSame(
            ['v2-tag-pak-any">Pak問わず', 'v2-tag-pak-64">Pak64', 'v2-tag-pak-other">Pak256'],
            $this->pakTagsOf($html, $multi->title),
        );
        $this->assertSame(['v2-tag-pak-128-japan">Pak128.Japan'], $this->pakTagsOf($html, $japan->title));

        $this->assertSame(['v2-tag-pak-128-japan">Pak128.Japan'], $this->pakTagsOf(
            (string) $this->get(route('index', ['pak' => '128-japan']))->assertOk()->getContent(),
            $japan->title,
        ));
    }

    /**
     * デプロイ直後の旧ファイル（paks が無い）や paks が壊れたファイルでも、新着は出して pak のタグだけを省く
     */
    public function test_cards_without_valid_paks_keep_latest_and_hide_pak_tags(): void
    {
        $article = $this->createArticleInPaks('pak の無い保存データの記事', ['64']);
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();
        $path = config()->string('public-article-snapshot.path');
        $snapshot = json_decode((string) File::get($path), true);

        foreach ([null, 'Pak64', ['64' => '64'], [64], ['64', null]] as $paks) {
            $broken = $snapshot;
            $broken['latest']['64'] = array_map(function (array $card) use ($paks): array {
                unset($card['paks']);

                return $paks === null ? $card : [...$card, 'paks' => $paks];
            }, $broken['latest']['64']);
            File::put($path, json_encode($broken));

            $this->get(route('index'))->assertOk()
                ->assertSee($article->title)
                ->assertDontSee('v2-tag-pak', false);
            $this->get(route('latest'))->assertOk()
                ->assertSee($article->title)
                ->assertDontSee('v2-tag-pak', false);
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
     */
    private function createArticleInPaks(string $title, array $paks): Article
    {
        $article = $this->createAddonIntroduction();
        $article->update(['title' => $title]);
        $categories = Category::where('type', CategoryType::Pak)->whereIn('slug', $paks)->pluck('id');
        $this->assertCount(count($paks), $categories);
        $article->categories()->attach($categories);

        return $article;
    }

    /**
     * タイトルを含むカード（<a>…</a>）の中の pak のタグ（色のクラスと表示名）
     *
     * @return list<string>
     */
    private function pakTagsOf(string $html, string $title): array
    {
        $this->assertSame(1, preg_match('/<a\s[^>]*>(?:(?!<\/a>).)*'.preg_quote(e($title), '/').'(?:(?!<\/a>).)*<\/a>/s', $html, $card));
        preg_match_all('/<span class="v2-tag-pak (v2-tag-pak-[a-z0-9-]+)[^"]*">([^<]+)<\/span>/', $card[0], $tags, PREG_SET_ORDER);

        return array_map(fn (array $tag): string => $tag[1].'">'.$tag[2], $tags);
    }
}
