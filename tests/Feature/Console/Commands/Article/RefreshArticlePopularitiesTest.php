<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands\Article;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\Feature\TestCase;

/**
 * 一覧の人気順の順位（前日までの直近 90 日の CV 合計の多い順）の作り直し（#683）
 */
class RefreshArticlePopularitiesTest extends TestCase
{
    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        // 記事 ID とユーザー ID が重ならないよう、ユーザーを先に多めに作る
        User::factory()->count(20)->create();
        $this->user = User::factory()->create();
        $this->travelTo(CarbonImmutable::parse('2026-05-10 04:30:00'));
    }

    public function test_前日までの90日の日次行だけを合計し期間外と日次以外は数えない(): void
    {
        $article = $this->makeArticle('2026-01-01 00:00:00');
        // 期間は 2026-02-09〜2026-05-09（両端を含む 90 日）
        $this->addCount($article, 1, '20260209', 5);
        $this->addCount($article, 1, '20260509', 3);
        $this->addCount($article, 1, '20260301', 1);
        $this->addCount($article, 1, '20260208', 100);
        $this->addCount($article, 1, '20260510', 50);
        $this->addCount($article, 2, '202605', 1000);
        $this->addCount($article, 3, '2026', 2000);
        $this->addCount($article, 4, 'total', 9999);

        $this->artisan('article:refresh-article-popularities')->assertSuccessful();

        $this->assertSame([$article->id => [1, 9]], $this->popularities());
    }

    public function test_全記事に_c_v合計・公開日時・idの降順で1から順位を振る(): void
    {
        // 同じ CV の tie と old は、id の小さい tie を公開が新しい記事にして、公開日時で並べないと順が逆になるようにする
        $tie = $this->makeArticle('2026-02-01 00:00:00');
        $old = $this->makeArticle('2026-01-01 00:00:00');
        $popular = $this->makeArticle('2025-01-01 00:00:00');
        $newer = $this->makeArticle('2026-03-01 00:00:00');
        $sameTimeLater = $this->makeArticle('2026-03-01 00:00:00');
        $this->addCount($popular, 1, '20260501', 7);
        $this->addCount($popular, 1, '20260502', 4);
        $this->addCount($tie, 1, '20260501', 2);
        $this->addCount($old, 1, '20260501', 2);
        $this->addCount($newer, 1, '20260101', 30);

        $this->artisan('article:refresh-article-popularities')
            ->expectsOutputToContain('CV のある記事 3 件、順位 5 件')
            ->assertSuccessful();

        $this->assertSame([
            $popular->id => [1, 11],
            // 同じ CV は公開が新しい方が先
            $tie->id => [2, 2],
            $old->id => [3, 2],
            // CV が無い（期間外だけの）記事も 0 で行を持つ。公開日時も同じなら id の大きい方が先
            $sameTimeLater->id => [4, 0],
            $newer->id => [5, 0],
        ], $this->popularities());
    }

    public function test_削除済み・非公開の記事にも順位を振り作り直すと前回の行は残らない(): void
    {
        $trashed = $this->makeArticle('2026-01-01 00:00:00');
        $draft = $this->makeArticle('2026-01-02 00:00:00');
        $draft->update(['status' => ArticleStatus::Draft]);
        $this->addCount($trashed, 1, '20260501', 6);
        $this->addCount($draft, 1, '20260501', 8);
        $trashed->delete();
        DB::table('article_popularities')->insert(['article_id' => $draft->id, 'rank' => 99, 'conversion_count' => 1]);

        $this->artisan('article:refresh-article-popularities')->assertSuccessful();
        $this->artisan('article:refresh-article-popularities')->assertSuccessful();

        $this->assertSame([$draft->id => [1, 8], $trashed->id => [2, 6]], $this->popularities());
    }

    public function test_記事を物理削除すると順位の行も消える(): void
    {
        $article = $this->makeArticle('2026-01-01 00:00:00');
        $other = $this->makeArticle('2026-01-02 00:00:00');
        $this->artisan('article:refresh-article-popularities')->assertSuccessful();

        $article->forceDelete();

        $this->assertSame([$other->id => [1, 0]], $this->popularities());
    }

    public function test_作り直しに失敗したら前回の順位を残す(): void
    {
        $article = $this->makeArticle('2026-01-01 00:00:00');
        $this->artisan('article:refresh-article-popularities')->assertSuccessful();
        $this->addCount($article, 1, '20260501', 5);
        // DELETE の後の INSERT で失敗させ、DELETE が取り消されることを確かめる
        DB::connection()->beforeExecuting(function (string $query): void {
            if (str_starts_with($query, 'insert into `article_popularities`')) {
                throw new RuntimeException('failed');
            }
        });

        $this->artisan('article:refresh-article-popularities')->assertFailed();

        $this->assertSame([$article->id => [1, 0]], $this->popularities());
    }

    private function makeArticle(string $publishedAt): Article
    {
        $article = $this->createAddonPost($this->user);
        $article->update(['published_at' => $publishedAt]);

        return $article;
    }

    private function addCount(Article $article, int $type, string $period, int $count): void
    {
        DB::table('conversion_counts')->insert([
            'user_id' => $article->user_id,
            'article_id' => $article->id,
            'type' => $type,
            'period' => $period,
            'count' => $count,
        ]);
    }

    /**
     * @return array<int, array{int, int}> 記事 ID => [順位, CV 合計]（順位順）
     */
    private function popularities(): array
    {
        return DB::table('article_popularities')
            ->orderBy('rank')
            ->get()
            ->mapWithKeys(fn (object $row): array => [(int) $row->article_id => [(int) $row->rank, (int) $row->conversion_count]])
            ->all();
    }
}
