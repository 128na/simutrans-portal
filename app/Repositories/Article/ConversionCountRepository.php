<?php

declare(strict_types=1);

namespace App\Repositories\Article;

use App\Enums\ArticleAnalyticsType;
use App\Models\Article;
use App\Models\User;
use App\Repositories\Article\Concerns\BuildsPeriodCountQuery;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class ConversionCountRepository
{
    use BuildsPeriodCountQuery;

    /**
     * 日次、月次、年次、全体の合計をカウントアップする.
     */
    public function countUp(Article $article, ?CarbonImmutable $datetime = null): void
    {
        $datetime ??= now();

        DB::transaction(function () use ($article, $datetime): void {
            DB::statement(
                $this->periodCountSql('conversion_counts'),
                $this->periodCountBindings($article, $datetime)
            );
        }, 10);
    }

    /**
     * 記事ごとの日次の合計を、期間（Ymd、両端を含む）で返す（記事の状態は問わない）.
     *
     * ロックしない読み取りで済ませ、CV の記録（countUp の upsert）を待たせない。
     *
     * @return array<int, int> キーは記事 ID。期間内に CV が無い記事は含まない
     */
    public function sumDailyByArticle(string $from, string $to): array
    {
        return DB::table('conversion_counts')
            ->where('type', ArticleAnalyticsType::Daily)
            ->whereBetween('period', [$from, $to])
            ->groupBy('article_id')
            ->selectRaw('article_id, SUM(count) AS total')
            ->pluck('total', 'article_id')
            ->mapWithKeys(fn (mixed $total, mixed $articleId): array => [(int) $articleId => (int) $total])
            ->all();
    }

    /**
     * ユーザー全体の日次の合計を、期間（Ymd）内の日ごとに返す.
     *
     * @return array<string, int> キーは Ymd
     */
    public function getDailyTotals(User $user, string $from, string $to): array
    {
        return $this->dailyTotalsOf('conversion_counts', $user, $from, $to);
    }
}
