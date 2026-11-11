<?php

declare(strict_types=1);

namespace App\Repositories\Article;

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
     * ユーザー全体の日次の合計を、期間（Ymd）内の日ごとに返す.
     *
     * @return array<string, int> キーは Ymd
     */
    public function getDailyTotals(User $user, string $from, string $to): array
    {
        return $this->dailyTotalsOf('conversion_counts', $user, $from, $to);
    }
}
