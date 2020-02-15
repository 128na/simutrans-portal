<?php
namespace App\Traits;

use App\Models\Article;
use Illuminate\Support\Facades\DB;

/**
 * 日次、月次、年次、全体の合計をカウントアップする
 */
trait CountUpable
{
    public static $types = [
        'daily'   => 1,
        'monthly' => 2,
        'yearly'  => 3,
        'total'   => 4,
    ];

    public static function getTableName()
    {
        throw new Exception('未実装');
    }

    public static function countUp(Article $article, $datetime = null)
    {
        $datetime = $datetime ?? now();
        $sql = self::buildSql($article, $datetime);

        DB::transaction(function () use ($sql) {
            DB::statement($sql);
        }, 10);
    }

    private static function buildSql(Article $article, $datetime)
    {
        $table   = static::getTableName();
        $dayly   = $datetime->format('Ymd');
        $monthly = $datetime->format('Ym');
        $yearly  = $datetime->format('Y');
        $total   = 'total';

        return "INSERT INTO {$table}(article_id, type, period, count)
            VALUES
                ({$article->id}, 1,'{$dayly}', 1),
                ({$article->id}, 2,'{$monthly}', 1),
                ({$article->id}, 3,'{$yearly}', 1),
                ({$article->id}, 4,'total', 1)
            ON DUPLICATE KEY UPDATE
                count = count + 1;";
    }
}
