<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Mypage\DashboardController;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\Feature\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_未ログイン(): void
    {
        $testResponse = $this->get('/mypage');
        $testResponse->assertRedirect('/login');
    }

    public function test_投稿ボタンが先頭にあり今月の数字をカードで示す(): void
    {
        $article = Article::factory()->for($this->user)->publish()->create();
        Article::factory()->for($this->user)->draft()->create();
        $this->insertCount('view_counts', $article, 2, now()->format('Ym'), 1234);
        $this->insertCount('conversion_counts', $article, 2, now()->format('Ym'), 56);

        $testResponse = $this->actingAs($this->user)->get('/mypage');

        $testResponse->assertOk();
        $testResponse->assertSeeInOrder([
            'アドオンを投稿する',
            '今月の表示回数（PV）', '1,234',
            '今月のダウンロード（CV）', '56',
            '公開中の記事', '1', '/ 全 2 件',
            'ファイル使用量',
            '直近30日の推移',
            '最近更新した記事',
        ]);
        $testResponse->assertSee(route('mypage.articles.create'), false);
    }

    public function test_最近更新した記事を5件まで状態と累計とともに表示する(): void
    {
        $published = Article::factory()->for($this->user)->publish()->create([
            'title' => '記事1日前',
            'modified_at' => now()->subDay(),
        ]);
        foreach (range(2, 6) as $daysAgo) {
            Article::factory()->for($this->user)->publish()->create([
                'title' => "記事{$daysAgo}日前",
                'modified_at' => now()->subDays($daysAgo),
            ]);
        }
        $draft = Article::factory()->for($this->user)->draft()->create([
            'title' => '書きかけの記事',
            'modified_at' => now(),
        ]);
        $this->insertCount('view_counts', $published, 4, 'total', 777);
        $this->insertCount('conversion_counts', $published, 4, 'total', 88);

        $testResponse = $this->actingAs($this->user)->get('/mypage');

        $testResponse->assertOk();
        $testResponse->assertSeeInOrder([
            '書きかけの記事', '下書き',
            '記事1日前', '公開', '777', '88',
            '記事4日前',
        ]);
        // 下書き1件＋公開4件の5件まで
        $testResponse->assertDontSee('記事5日前');
        $testResponse->assertSee(route('mypage.articles.edit', $draft), false);
        $testResponse->assertSee($published->showUrl(), false);
        $testResponse->assertDontSee($draft->showUrl(), false);
    }

    /**
     * 最近の記事の表は横スクロール枠で、見出しに sr-only（絶対配置）の「操作」がある。
     * 枠を位置指定しないと sr-only が枠の外にはみ出し、スマホ幅でページ全体が横にスクロールする（#669）
     */
    public function test_最近の記事の表の横スクロール枠が見出しの読み上げ用テキストをはみ出させない(): void
    {
        Article::factory()->for($this->user)->publish()->create();

        $html = $this->actingAs($this->user)->get('/mypage')->assertOk()->getContent();

        $this->assertIsString($html);
        $this->assertMatchesRegularExpression('/<div class="(?=[^"]*\bv2-table-wrapper\b)(?=[^"]*\brelative\b)[^"]*">\s*<table[^>]*>(?:(?!<\/table>).)*<span class="sr-only">操作<\/span>/su', $html);
    }

    public function test_直近30日の推移はユーザー全体の日次集計を日ごとに合計して期間内だけ渡す(): void
    {
        $published = Article::factory()->for($this->user)->publish()->create();
        $other = Article::factory()->for($this->user)->publish()->create();
        // 下書きやゴミ箱の記事も、今月のカードと同じくユーザー全体の合計に含める
        $draft = Article::factory()->for($this->user)->draft()->create();
        $trashed = Article::factory()->for($this->user)->create(['status' => ArticleStatus::Trash]);
        $stranger = Article::factory()->for(User::factory()->create())->publish()->create();
        $today = now()->format('Ymd');
        $firstDay = now()->subDays(29)->format('Ymd');
        $tooOld = now()->subDays(30)->format('Ymd');
        $this->insertCount('view_counts', $published, 1, $today, 9);
        $this->insertCount('view_counts', $other, 1, $today, 20);
        $this->insertCount('view_counts', $draft, 1, $today, 300);
        $this->insertCount('view_counts', $published, 1, $firstDay, 3);
        $this->insertCount('view_counts', $trashed, 1, $firstDay, 40);
        $this->insertCount('view_counts', $published, 1, $tooOld, 1000);
        // 日次以外（月次・累計）や他ユーザーの値は入らない
        $this->insertCount('view_counts', $published, 2, now()->format('Ym'), 5000);
        $this->insertCount('view_counts', $published, 4, 'total', 6000);
        $this->insertCount('view_counts', $stranger, 1, $today, 70000);
        $this->insertCount('conversion_counts', $other, 1, $today, 2);
        $this->insertCount('conversion_counts', $draft, 1, $today, 5);
        $this->insertCount('conversion_counts', $stranger, 1, $today, 800);

        $testResponse = $this->actingAs($this->user)->get('/mypage');

        $testResponse->assertOk();
        $trend = $this->extractTrendJson($testResponse->getContent());
        $this->assertSame(now()->subDays(29)->format('Y-m-d'), $trend['start_date']);
        $this->assertSame(now()->format('Y-m-d'), $trend['end_date']);
        $this->assertNotNull($trend['total']);
        $this->assertEquals([$today => 329, $firstDay => 43], $trend['total']['viewCounts']);
        $this->assertEquals([$today => 7], $trend['total']['conversionCounts']);
        $this->assertSame(0, $trend['total']['pastViewCount']);
        $this->assertSame(0, $trend['total']['pastConversionCount']);
        $testResponse->assertSee('すべての記事の合計');
    }

    public function test_公開中の記事があって集計が無ければ空の合計を渡す(): void
    {
        Article::factory()->for($this->user)->publish()->create();

        $testResponse = $this->actingAs($this->user)->get('/mypage');

        $testResponse->assertOk();
        $trend = $this->extractTrendJson($testResponse->getContent());
        $this->assertSame([], $trend['total']['viewCounts'] ?? null);
        $this->assertSame([], $trend['total']['conversionCounts'] ?? null);
    }

    public function test_公開中の記事が無ければ推移の合計は渡さない(): void
    {
        $draft = Article::factory()->for($this->user)->draft()->create();
        $this->insertCount('view_counts', $draft, 1, now()->format('Ymd'), 50);

        $testResponse = $this->actingAs($this->user)->get('/mypage');

        $testResponse->assertOk();
        $this->assertNull($this->extractTrendJson($testResponse->getContent())['total']);
    }

    public function test_記事が無いときは推移の合計を渡さない(): void
    {
        $testResponse = $this->actingAs($this->user)->get('/mypage');

        $testResponse->assertOk();
        $this->assertNull($this->extractTrendJson($testResponse->getContent())['total']);
        $testResponse->assertSee('まだ記事がありません。');
    }

    public function test_二要素認証が無効なら帯で知らせる(): void
    {
        $testResponse = $this->actingAs($this->user)->get('/mypage');

        $testResponse->assertOk();
        $testResponse->assertSee('二要素認証が無効です');
        $testResponse->assertSee(route('mypage.two-factor'), false);
    }

    public function test_二要素認証が有効なら帯を出さない(): void
    {
        $this->user->forceFill(['two_factor_secret' => encrypt('secret')])->save();

        $testResponse = $this->actingAs($this->user)->get('/mypage');

        $testResponse->assertOk();
        $testResponse->assertDontSee('二要素認証が無効です');
    }

    public function test_メールアドレスが未認証なら帯で認証へ案内する(): void
    {
        $this->user->forceFill(['email_verified_at' => null])->save();

        $testResponse = $this->actingAs($this->user)->get('/mypage');

        $testResponse->assertOk();
        $testResponse->assertSee('メールアドレスの認証が完了していません');
        $testResponse->assertSee(route('mypage.verify-email'), false);
        // 未認証のうちは二要素認証の設定に入れないので、その帯は出さない
        $testResponse->assertDontSee('二要素認証が無効です');
    }

    public function test_メールアドレスが認証済みなら認証の帯を出さない(): void
    {
        $testResponse = $this->actingAs($this->user)->get('/mypage');

        $testResponse->assertOk();
        $testResponse->assertDontSee('メールアドレスの認証が完了していません');
    }

    public function test_管理者にだけメニューに管理画面を出す(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/mypage')
            ->assertOk()
            ->assertSee(route('admin.index'), false);
        $this->actingAs($this->user)->get('/mypage')
            ->assertOk()
            ->assertDontSee(route('admin.index'), false);
    }

    public function test_記事が多くてもクエリ数は変わらない(): void
    {
        $few = $this->countQueriesForUserWithArticles(1);
        $many = $this->countQueriesForUserWithArticles(30);

        $this->assertSame($few, $many);
        // サマリ1・最近の記事3（本体と累計PV/CV）・推移2（ユーザー全体の日次PV/CV合計）
        $this->assertSame(6, $many);
    }

    private function countQueriesForUserWithArticles(int $articleCount): int
    {
        $user = User::factory()->create();
        $articles = Article::factory()->for($user)->publish()->count($articleCount)->create();
        foreach ($articles as $article) {
            $this->insertCount('view_counts', $article, 1, now()->format('Ymd'), 1);
            $this->insertCount('view_counts', $article, 4, 'total', 10);
        }

        $this->actingAs($user);
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->get('/mypage')->assertOk();
        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $count;
    }

    /**
     * 画面に埋め込んだ推移のJSON（フロントが読むもの）を取り出す
     *
     * @return array{start_date:string,end_date:string,total:array{id:int,viewCounts:array<string,int>,conversionCounts:array<string,int>,pastViewCount:int,pastConversionCount:int}|null}
     */
    private function extractTrendJson(false|string $html): array
    {
        $this->assertIsString($html);
        if (preg_match('#<script id="data-trend" type="application/json">(.*?)</script>#s', $html, $matches) !== 1) {
            $this->fail('推移のJSONが埋め込まれていない');
        }

        /** @var array{start_date:string,end_date:string,total:array{id:int,viewCounts:array<string,int>,conversionCounts:array<string,int>,pastViewCount:int,pastConversionCount:int}|null} */
        return json_decode($matches[1], true, flags: JSON_THROW_ON_ERROR);
    }

    private function insertCount(string $table, Article $article, int $type, string $period, int $count): void
    {
        DB::table($table)->insert([
            'article_id' => $article->id,
            'user_id' => $article->user_id,
            'type' => $type,
            'period' => $period,
            'count' => $count,
        ]);
    }
}
