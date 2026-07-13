<?php

declare(strict_types=1);

namespace App\Repositories\Article\Concerns;

use App\Models\Article;
use Carbon\CarbonImmutable;

/**
 * 日次・月次・年次・全体の集計カウントアップ用SQLを組み立てる.
 *
 * ViewCountRepository / ConversionCountRepository で共通のロジック。
 */
trait BuildsPeriodCountQuery
{
    private function periodCountSql(string $table): string
    {
        return "INSERT INTO {$table}(user_id, article_id, type, period, count)
            VALUES
                (?, ?, 1, ?, 1),
                (?, ?, 2, ?, 1),
                (?, ?, 3, ?, 1),
                (?, ?, 4, ?, 1)
            ON DUPLICATE KEY UPDATE
                count = count + 1;";
    }

    /**
     * @return array<int, int|string|null>
     */
    private function periodCountBindings(Article $article, CarbonImmutable $datetime): array
    {
        $daily = $datetime->format('Ymd');
        $monthly = $datetime->format('Ym');
        $yearly = $datetime->format('Y');
        $total = 'total';

        return [
            $article->user_id, $article->id, $daily,
            $article->user_id, $article->id, $monthly,
            $article->user_id, $article->id, $yearly,
            $article->user_id, $article->id, $total,
        ];
    }
}
