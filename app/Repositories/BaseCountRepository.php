<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Article;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * @deprecated このクラスは非推奨です。ViewCountRepositoryとConversionCountRepositoryに機能がインライン化されました。
 */
abstract class BaseCountRepository
{
    /**
     * 日次、月次、年次、全体の合計をカウントアップする.
     */
    final public function countUp(Article $article, ?CarbonImmutable $datetime = null): void
    {
        $datetime ??= now();
        $sql = $this->buildSql($article, $datetime);

        DB::transaction(function () use ($sql): void {
            DB::statement($sql);
        }, 10);
    }

    /**
     * テーブル名を返す.
     */
    abstract public function getTableName(): string;

    private function buildSql(Article $article, CarbonImmutable $datetime): string
    {
        $table = $this->getTableName();
        $dayly = $datetime->format('Ymd');
        $monthly = $datetime->format('Ym');
        $yearly = $datetime->format('Y');
        $total = 'total';

        return "INSERT INTO {$table}(user_id, article_id, type, period, count)
            VALUES
                ({$article->user_id}, {$article->id}, 1,'{$dayly}', 1),
                ({$article->user_id}, {$article->id}, 2,'{$monthly}', 1),
                ({$article->user_id}, {$article->id}, 3,'{$yearly}', 1),
                ({$article->user_id}, {$article->id}, 4,'{$total}', 1)
            ON DUPLICATE KEY UPDATE
                count = count + 1;";
    }
}
