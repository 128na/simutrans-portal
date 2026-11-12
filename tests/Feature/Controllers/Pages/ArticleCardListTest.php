<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Pages;

use App\Enums\ArticleAnalyticsType;
use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\TestCase;

/**
 * pak 別・タグ・ユーザー・検索結果・pak×種類の一覧（カード表示）の絞り込み・並び順
 */
class ArticleCardListTest extends TestCase
{
    private User $user;

    private Tag $tag;

    private Category $pak;

    private Category $trains;

    private Category $buildings;

    /** 公開は一番新しいが、更新は2番目 */
    private Article $postTrains;

    /** 公開は一番古いが、更新は一番新しい（アドオン紹介） */
    private Article $introTrains;

    /** 公開・更新とも中間〜一番古い */
    private Article $postBuildings;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        // 記事・ユーザー・タグの ID がカテゴリ ID などと重ならないよう、無関係なデータを先に作る
        User::factory()->count(3)->create();
        Tag::factory()->count(4)->create();
        for ($i = 0; $i < 5; $i++) {
            $this->createPage();
        }

        $this->user = User::factory()->create();
        $this->tag = Tag::factory()->create();
        $this->pak = Category::where('type', CategoryType::Pak)->where('slug', '128-japan')->firstOrFail();
        $this->trains = Category::where('type', CategoryType::Addon)->where('slug', 'trains')->firstOrFail();
        $this->buildings = Category::where('type', CategoryType::Addon)->where('slug', 'buildings')->firstOrFail();

        $this->postTrains = $this->makeArticle(true, $this->trains, '2026-01-03 00:00:00', '2026-01-04 00:00:00');
        $this->introTrains = $this->makeArticle(false, $this->trains, '2026-01-01 00:00:00', '2026-01-05 00:00:00');
        $this->postBuildings = $this->makeArticle(true, $this->buildings, '2026-01-02 00:00:00', '2026-01-03 00:00:00');
    }

    /**
     * @return array<string, array{string}>
     */
    public static function listProvider(): array
    {
        return [
            'pak' => ['pak'],
            'tag' => ['tag'],
            'user' => ['user'],
            'search' => ['search'],
        ];
    }

    #[DataProvider('listProvider')]
    public function test_既定は更新が新しい順(string $list): void
    {
        $this->assertIds($list, [], [$this->introTrains, $this->postTrains, $this->postBuildings]);
    }

    #[DataProvider('listProvider')]
    public function test_公開が新しい順に並べ替えられる(string $list): void
    {
        $this->assertIds($list, ['sort' => 'published'], [$this->postTrains, $this->postBuildings, $this->introTrains]);
    }

    /**
     * 人気順は集計した順位表の順。直近の CV が多い順で、同じ CV は公開が新しい順（#683）
     */
    #[DataProvider('listProvider')]
    public function test_人気順は直近の_c_vが多い順で同じ_c_vは公開が新しい順(string $list): void
    {
        $yesterday = now()->subDay()->format('Ymd');
        // 同じ CV の postTrains と introTrains は、公開が新しい postTrains が先（id は introTrains の方が大きい）
        $this->addConversion($this->postTrains, $yesterday, 3);
        $this->addConversion($this->introTrains, $yesterday, 3);
        $this->addConversion($this->postBuildings, $yesterday, 10);
        $this->artisan('article:refresh-article-popularities')->assertSuccessful();

        $this->assertIds($list, ['sort' => 'popular'], [$this->postBuildings, $this->postTrains, $this->introTrains]);
        $this->assertIds($list, [], [$this->introTrains, $this->postTrains, $this->postBuildings], '既定の更新順は人気順に左右されない');
        $this->assertIds($list, ['sort' => 'published'], [$this->postTrains, $this->postBuildings, $this->introTrains], '公開順は人気順に左右されない');
        $this->assertIds($list, ['sort' => 'popular', 'addon' => 'trains'], [$this->postTrains, $this->introTrains]);
    }

    /**
     * 一覧は順位表の順位の順に並べ、公開状態は表示時に絞る。順位表に行が無い記事（最後の作り直しより後に作られた記事）は次の作り直しまで出ない
     */
    #[DataProvider('listProvider')]
    public function test_人気順は順位の順に並べ非公開の記事と順位の無い記事は出さない(string $list): void
    {
        $draft = $this->makeArticle(true, $this->trains, '2026-01-06 00:00:00', '2026-01-06 00:00:00');
        $unranked = $this->makeArticle(true, $this->trains, '2026-01-07 00:00:00', '2026-01-07 00:00:00');
        $draft->update(['status' => ArticleStatus::Draft]);
        $this->setRank($this->introTrains, 1);
        $this->setRank($draft, 2);
        $this->setRank($this->postBuildings, 3);
        $this->setRank($this->postTrains, 4);

        $this->assertIds($list, ['sort' => 'popular'], [$this->introTrains, $this->postBuildings, $this->postTrains]);
        $this->assertIds($list, [], [$unranked, $this->introTrains, $this->postTrains, $this->postBuildings], 'ほかの並び順には出る');
    }

    public function test_pak種類別の一覧も人気順に並べられる(): void
    {
        $this->setRank($this->postTrains, 1);
        $this->setRank($this->introTrains, 2);
        $url = route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'trains', 'sort' => 'popular']);

        $this->assertSame([$this->postTrains->id, $this->introTrains->id], $this->idsOf($this->get($url)));

        // 順位を入れ替えると並びも入れ替わる（更新順・公開順・id のどれでもなく順位で並べている）
        DB::table('article_popularities')->delete();
        $this->setRank($this->introTrains, 1);
        $this->setRank($this->postTrains, 2);
        $this->assertSame([$this->introTrains->id, $this->postTrains->id], $this->idsOf($this->get($url)));
    }

    public function test_その他のpakも人気順に並べページ送りの件数は順位のある記事だけ(): void
    {
        $any = Category::where('type', CategoryType::Pak)->where('slug', 'any')->firstOrFail();
        $articles = [];
        for ($i = 0; $i < 26; $i++) {
            $article = $this->createAddonPost($this->user);
            $article->update(['published_at' => '2025-12-01 00:00:00', 'modified_at' => '2025-12-01 00:00:00']);
            $article->categories()->attach([$any->id, $this->trains->id]);
            $articles[] = $article;
        }
        // 作った順に順位を付ける（id の降順とは逆）。最後の 1 件は順位が無い（作り直しより後に作られた記事）
        foreach (array_slice($articles, 0, 25) as $index => $article) {
            $this->setRank($article, $index + 1);
        }

        $first = $this->get(route('pak.others', ['sort' => 'popular']))->assertOk();
        $second = $this->get(route('pak.others', ['sort' => 'popular', 'page' => 2]))->assertOk();

        $this->assertSame(array_map(fn (Article $article): int => $article->id, array_slice($articles, 0, 24)), $this->idsOf($first));
        $this->assertSame([$articles[24]->id], $this->idsOf($second));
        /** @var AnonymousResourceCollection $paginated */
        $paginated = $first->viewData('articles');
        $this->assertSame(25, $paginated->resource->total(), '件数は順位のある記事だけを数え、ページ送りと合う');
    }

    public function test_人気順を選ぶと補足を出し並び順の切り替えが条件を保つ(): void
    {
        $html = $this->get($this->url('pak', ['addon' => 'trains', 'sort' => 'popular']))->assertOk()->getContent();

        $this->assertIsString($html);
        $this->assertMatchesRegularExpression('/aria-current="true"\s+title="[^"]*前日までの 90 日間の入手数[^"]*"\s*>人気順</u', $html);
        $this->assertStringContainsString('<p class="px-1 text-note text-c-sub">人気順：前日までの 90 日間の入手数（ダウンロードと掲載先クリック）の目安</p>', $html);
        $this->assertMatchesRegularExpression('/href="[^"]*addon=buildings[^"]*sort=popular[^"]*"/', $html, '種類を切り替えても人気順を保つ');

        $other = $this->get($this->url('pak', []))->assertOk()->getContent();
        $this->assertIsString($other);
        $this->assertMatchesRegularExpression('/href="[^"]*sort=popular[^"]*"[^>]*>人気順</u', $other, 'ほかの並び順からも人気順を選べる');
        $this->assertStringNotContainsString('<p class="px-1 text-note text-c-sub">人気順：', $other, '補足は人気順を選んだときだけ出す');
    }

    #[DataProvider('listProvider')]
    public function test_種類で絞り込める(string $list): void
    {
        $this->assertIds($list, ['addon' => 'buildings'], [$this->postBuildings]);
        $this->assertIds($list, ['addon' => 'trains'], [$this->introTrains, $this->postTrains]);
    }

    #[DataProvider('listProvider')]
    public function test_ダウンロードできるものだけに絞り込める(string $list): void
    {
        $this->assertIds($list, ['postTypes' => ['addon-post']], [$this->postTrains, $this->postBuildings]);
        $this->assertIds($list, ['postTypes' => ['addon-post'], 'addon' => 'trains', 'sort' => 'published'], [$this->postTrains]);
    }

    #[DataProvider('listProvider')]
    public function test_不正な値は既定の条件として扱う(string $list): void
    {
        $this->assertIds($list, ['sort' => 'unknown', 'addon' => 'unknown-addon'], [$this->introTrains, $this->postTrains, $this->postBuildings]);
        $this->assertIds($list, ['sort' => ['published'], 'addon' => ['trains']], [$this->introTrains, $this->postTrains, $this->postBuildings]);
    }

    public function test_pak種類別の一覧も並び順と入手方法で絞り込める(): void
    {
        $url = route('categories.pakAddon', ['pak' => '128-japan', 'addon' => 'trains']);

        $this->assertSame([$this->introTrains->id, $this->postTrains->id], $this->idsOf($this->get($url)));
        $this->assertSame([$this->postTrains->id], $this->idsOf($this->get($url.'?postTypes[]=addon-post')));
        $this->get($url)->assertDontSee('aria-label="種類で絞り込む"', false);
        $this->assertSame([$this->introTrains->id, $this->postTrains->id], $this->idsOf($this->get($url.'?addon=buildings')), '種類の切り替えを出さない一覧では ?addon を無視する');
    }

    /**
     * 種類の行は横スクロール枠で、中の件数に sr-only（絶対配置）の「件」がある。
     * 枠を位置指定しないと sr-only が枠の外にはみ出し、スマホ幅でページ全体が横にスクロールする（#656）
     */
    public function test_種類の絞り込みの横スクロール枠が中の読み上げ用テキストをはみ出させない(): void
    {
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $html = $this->get($this->url('pak', []))->assertOk()->getContent();

        $this->assertIsString($html);
        $this->assertMatchesRegularExpression('/<nav aria-label="種類で絞り込む" class="[^"]*"(?:(?!<\/nav>).)*<span class="sr-only">件<\/span>/su', $html, '種類の行の中に件数の読み上げ用テキストがある');
        $this->assertMatchesRegularExpression('/<nav aria-label="種類で絞り込む" class="(?=[^"]*\brelative\b)(?=[^"]*\boverflow-x-auto\b)[^"]*"/u', $html);
    }

    /**
     * 件数がある pak 別一覧は 0 件の種類を出さない。件数が無い一覧（タグ等）は少ない種類が分からないのですべて出す（#657）
     */
    public function test_件数がある一覧だけ0件の種類を種類の切り替えに出さない(): void
    {
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $pak = $this->get($this->url('pak', ['sort' => 'published']))->assertOk()->getContent();
        $this->assertIsString($pak);
        $this->assertMatchesRegularExpression('/href="(?=[^"]*addon=trains)(?=[^"]*sort=published)[^"]*"/', $pak);
        $this->assertMatchesRegularExpression('/href="(?=[^"]*addon=buildings)(?=[^"]*sort=published)[^"]*"/', $pak);
        $this->assertStringNotContainsString('addon=ships', $pak);
        $this->assertDoesNotMatchRegularExpression('/ほか \d+ 種類/u', $pak, '種類が少なければ畳まない');

        $selected = $this->get($this->url('pak', ['addon' => 'ships']))->assertOk()->getContent();
        $this->assertIsString($selected);
        $this->assertMatchesRegularExpression('/class="v2-chip"\s+aria-current="true"\s*>船舶/u', $selected, '選択中の種類は 0 件でもチップを出す');

        $this->get($this->url('tag', []))
            ->assertOk()
            ->assertSee('addon=ships', false)
            ->assertSee('addon=none', false);
    }

    public function test_件数の少ない種類はほかn種類に畳み畳んだ切り替えも条件を保つ(): void
    {
        foreach (['rail-tools', 'road-tools', 'ships', 'aircrafts', 'road-vehicles'] as $slug) {
            $this->makeArticle(true, Category::where('type', CategoryType::Addon)->where('slug', $slug)->firstOrFail(), '2025-12-01 00:00:00', '2025-12-01 00:00:00');
        }
        $this->artisan('article:refresh-public-snapshot')->assertSuccessful();

        $html = $this->get($this->url('pak', ['sort' => 'published']))->assertOk()->getContent();

        $this->assertIsString($html);
        // 鉄道車両は 2 件、ほかは 1 件ずつ。同数は定義順なので、定義順で後ろの一般建築物だけが畳まれる
        $this->assertMatchesRegularExpression('/<details[^>]*>\s*<summary[^>]*>\s*<span[^>]*>ほか 1 種類<\/span>(?:(?!<\/details>).)*href="(?=[^"]*addon=buildings)(?=[^"]*sort=published)[^"]*"(?:(?!<\/details>).)*<\/details>/su', $html);
        $this->assertDoesNotMatchRegularExpression('/<details(?:(?!<\/details>).)*addon=(?:trains|ships)/su', $html, '件数の多い種類は畳まない');
    }

    public function test_検索画面では入手方法の切り替えを出さず投稿形式の条件を上書きしない(): void
    {
        $this->get($this->url('search', ['postTypes' => ['addon-introduction']]))
            ->assertOk()
            ->assertSee('aria-label="並び順"', false)
            ->assertDontSee('aria-label="入手方法で絞り込む"', false);
        $this->get($this->url('pak', []))->assertSee('aria-label="入手方法で絞り込む"', false);
    }

    #[DataProvider('listProvider')]
    public function test_日時が同じ記事は_i_dの新しい順に並べページ送りで重複させない(string $list): void
    {
        $this->introTrains->update(['published_at' => '2026-01-03 00:00:00']);

        $this->assertIds($list, ['sort' => 'published'], [$this->introTrains, $this->postTrains, $this->postBuildings]);
    }

    #[DataProvider('listProvider')]
    public function test_絞り込み・並び順を付けてもクエリ数は増えず件数の集計も1回だけ(string $list): void
    {
        $plain = $this->queriesOf($this->url($list, []));
        $filtered = $this->queriesOf($this->url($list, ['addon' => 'trains', 'sort' => 'published', 'postTypes' => ['addon-post']]));

        $this->assertCount(count($plain), $filtered);
        $this->assertCount(1, array_filter($filtered, fn (string $sql): bool => str_starts_with($sql, 'select count(')));
        $this->assertSame([], array_values(array_filter($filtered, fn (string $sql): bool => preg_match('/from `profiles`|from `tags` inner join `article_tag`/', $sql) === 1)), 'カードで使わないタグ・プロフィールは読み込まない');

        $this->artisan('article:refresh-article-popularities')->assertSuccessful();
        $popular = $this->queriesOf($this->url($list, ['addon' => 'trains', 'sort' => 'popular', 'postTypes' => ['addon-post']]));
        $this->assertCount(count($plain), $popular, '人気順でもクエリ数は増えない');
        $this->assertSame([], array_values(array_filter($popular, fn (string $sql): bool => str_contains($sql, 'conversion_counts'))), '表示時に CV を集計しない');
    }

    public function test_カードは既存のダウンロード・掲載先の経路を使い種別を見分けられる(): void
    {
        $response = $this->get($this->url('pak', []));

        $response->assertOk()->assertSee('id="app-article-cards"', false);
        $cards = collect($this->cardsOf($response))->keyBy('id');
        $this->assertSame(route('articles.download', ['article' => $this->postTrains->id]), $cards[$this->postTrains->id]['download_url']);
        $this->assertArrayNotHasKey('addon_page_url', $cards[$this->postTrains->id]);
        $this->assertSame(route('articles.conversion', ['article' => $this->introTrains->id]), $cards[$this->introTrains->id]['addon_page_url']);
        $this->assertArrayNotHasKey('download_url', $cards[$this->introTrains->id]);
        $this->assertSame(['id' => $this->user->id, 'name' => $this->user->name, 'url' => route('users.show', ['userIdOrNickname' => $this->user->nickname ?? $this->user->id])], $cards[$this->introTrains->id]['user']);
    }

    public function test_選択中の条件が分かり切り替えリンクとページ送りが条件を保つ(): void
    {
        for ($i = 0; $i < 24; $i++) {
            $this->makeArticle(true, $this->trains, '2025-12-01 00:00:00', '2025-12-01 00:00:00');
        }

        $response = $this->get($this->url('pak', ['addon' => 'trains', 'sort' => 'published']));

        $response->assertOk();
        $html = $response->getContent();
        $this->assertIsString($html);
        $this->assertMatchesRegularExpression('/href="[^"]*addon=trains[^"]*"\s+class="v2-chip"\s+aria-current="true"\s*>鉄道車両</', $html);
        $this->assertMatchesRegularExpression('/aria-current="true"\s*>公開が新しい順</', $html);
        $this->assertMatchesRegularExpression('/href="[^"]*addon=buildings[^"]*sort=published[^"]*"/', $html, '種類を切り替えても並び順を保つ');
        $this->assertMatchesRegularExpression('/href="[^"]*addon=trains[^"]*sort=published[^"]*page=2[^"]*"/', html_entity_decode($html), 'ページ送りが条件を保つ');
        $response->assertSee('26 件');
    }

    private function makeArticle(bool $post, Category $addon, string $publishedAt, string $modifiedAt): Article
    {
        $article = $post ? $this->createAddonPost($this->user) : $this->createAddonIntroduction($this->user);
        $article->update(['published_at' => $publishedAt, 'modified_at' => $modifiedAt]);
        $article->categories()->attach([$this->pak->id, $addon->id]);
        $article->tags()->attach($this->tag->id);

        return $article;
    }

    /**
     * @param  array<string, mixed>  $query
     */
    private function url(string $list, array $query): string
    {
        return match ($list) {
            'pak' => route('pak.128japan', $query),
            'tag' => route('tags.show', ['tag' => $this->tag, ...$query]),
            'user' => route('users.show', ['userIdOrNickname' => $this->user->id, ...$query]),
            'search' => route('search', ['categoryIds' => [$this->pak->id], ...$query]),
        };
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  list<Article>  $expected
     */
    private function assertIds(string $list, array $query, array $expected, string $message = ''): void
    {
        $this->assertSame(
            array_map(fn (Article $article): int => $article->id, $expected),
            $this->idsOf($this->get($this->url($list, $query))),
            trim($message.' '.json_encode($query, JSON_THROW_ON_ERROR)),
        );
    }

    private function setRank(Article $article, int $rank): void
    {
        DB::table('article_popularities')->insert(['article_id' => $article->id, 'rank' => $rank, 'conversion_count' => 0]);
    }

    private function addConversion(Article $article, string $period, int $count): void
    {
        DB::table('conversion_counts')->insert([
            'user_id' => $article->user_id,
            'article_id' => $article->id,
            'type' => ArticleAnalyticsType::Daily->value,
            'period' => $period,
            'count' => $count,
        ]);
    }

    /**
     * @return list<int>
     */
    private function idsOf(TestResponse $response): array
    {
        return array_map(fn (array $card): int => $card['id'], $this->cardsOf($response));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function cardsOf(TestResponse $response): array
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
     * @return list<string>
     */
    private function queriesOf(string $url): array
    {
        DB::enableQueryLog();
        DB::flushQueryLog();
        try {
            $this->get($url)->assertOk();

            return array_map(fn (array $query): string => $query['query'], DB::getQueryLog());
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }
    }
}
