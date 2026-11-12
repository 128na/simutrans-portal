<?php

declare(strict_types=1);

namespace App\Repositories\Article;

use App\Models\Article\ArticlePopularity;
use Illuminate\Support\Facades\DB;

class ArticlePopularityRepository
{
    /** 1 文で入れる行数 */
    private const int INSERT_CHUNK = 500;

    public function __construct(public ArticlePopularity $model) {}

    /**
     * 人気順の順位を全記事分作り直す
     *
     * 削除済み・非公開の記事も含めて全記事に行を持たせ（CV が無い記事は 0）、CV 合計・公開日時・id の降順で 1 から順位を振る。
     * 一覧は表示時に公開状態で絞るので、状態が変わっても作り直しを待たずに出し入れされる。
     * DELETE と一括 INSERT を 1 つのトランザクションで行い、失敗したら前回の順位を残す。読み取り側は前回の順位を読み続ける。
     *
     * @param  array<int, int>  $totals  記事 ID => CV 合計
     * @return int 作った行数
     */
    public function rebuild(array $totals): int
    {
        $articles = DB::table('articles')
            ->get(['id', 'published_at'])
            ->map(fn (object $article): array => [
                'id' => (int) $article->id,
                'published_at' => (string) $article->published_at,
                'count' => $totals[(int) $article->id] ?? 0,
            ])
            ->all();

        usort($articles, fn (array $a, array $b): int => [$b['count'], $b['published_at'], $b['id']] <=> [$a['count'], $a['published_at'], $a['id']]);

        $rows = [];
        foreach ($articles as $index => $article) {
            $rows[] = ['article_id' => $article['id'], 'rank' => $index + 1, 'conversion_count' => $article['count']];
        }

        DB::transaction(function () use ($rows): void {
            $this->model->newQuery()->delete();
            foreach (array_chunk($rows, self::INSERT_CHUNK) as $chunk) {
                $this->model->newQuery()->insert($chunk);
            }
        });

        return count($rows);
    }
}
