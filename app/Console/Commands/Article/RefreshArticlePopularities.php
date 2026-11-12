<?php

declare(strict_types=1);

namespace App\Console\Commands\Article;

use App\Repositories\Article\ArticlePopularityRepository;
use App\Repositories\Article\ConversionCountRepository;
use Illuminate\Console\Command;
use Throwable;

/**
 * 一覧の「人気順」の順位（前日までの直近 90 日の CV 合計の多い順）を作り直す（#683）
 *
 * 表示時には集計しない。CV の日次行（conversion_counts の type=1）をロックしない読み取りで合計し、順位表を全記事分作り直す。
 */
class RefreshArticlePopularities extends Command
{
    /** 集計する日数。前日までの、日が終わった分だけを数える */
    public const int DAYS = 90;

    protected $signature = 'article:refresh-article-popularities';

    protected $description = '一覧の人気順の順位（前日までの直近90日のCV合計の多い順）を作り直す';

    public function handle(ConversionCountRepository $conversionCounts, ArticlePopularityRepository $popularities): int
    {
        $startedAt = microtime(true);
        // CV の記録（countUp）と同じくアプリのタイムゾーンで日付を決める
        $to = now()->subDay();
        $from = $to->subDays(self::DAYS - 1);

        try {
            $totals = $conversionCounts->sumDailyByArticle($from->format('Ymd'), $to->format('Ymd'));
            $rows = $popularities->rebuild($totals);
        } catch (Throwable $throwable) {
            report($throwable);
            $this->error('人気順の順位を作り直せませんでした。前回の順位を使い続けます。');

            return self::FAILURE;
        }

        $this->info(sprintf(
            '人気順の順位を作り直しました。期間 %s〜%s、CV のある記事 %d 件、順位 %d 件、%d ms %s',
            $from->format('Y-m-d'),
            $to->format('Y-m-d'),
            count($totals),
            $rows,
            (int) round((microtime(true) - $startedAt) * 1000),
            now()->toIso8601String(),
        ));

        return self::SUCCESS;
    }
}
