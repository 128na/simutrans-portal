<?php

declare(strict_types=1);

namespace App\Console\Commands\Sns;

use App\Actions\SendSNS\Article\BuildXDigestText;
use App\Actions\SendSNS\Article\GetXDigestArticles;
use App\Enums\XDigestLogStatus;
use App\Repositories\XDigestLogRepository;
use App\Services\Twitter\Exceptions\TwitterApiRequestException;
use App\Services\Twitter\TwitterV2Api;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Throwable;

/**
 * X (Twitter) への新規・更新記事の通知を、1日2回(12:00/21:30 JST)の集約投稿へまとめて送る。
 * 前回の成功実行(x_digest_logs.status=success の cutoff_at)以降の公開・更新記事を対象とし、
 * 成功時のみ cutoff を進める。対象0件または投稿失敗時はXへ投稿しない(ADR-0005)。
 *
 * 投稿前に status=pending でログ行を書き込み、投稿結果に応じて success/failed へ更新する。
 * これにより、投稿成功後・ログ更新前にプロセスが強制終了した場合でも pending 行が
 * 監査可能な形で残る(この場合 latestCutoff() は pending を無視するため、次回実行は
 * 同じ対象期間を再計算する。Xの投稿作成APIにクライアント指定の冪等キーが無いため、
 * これによる再投稿リスクは完全には排除できない)。
 */
class XDailyDigestCommand extends Command
{
    protected $signature = 'sns:x-daily-digest';

    protected $description = '前回の成功配信以降の新規・更新記事をXへ1日2回集約投稿する';

    public function handle(
        XDigestLogRepository $xDigestLogRepository,
        GetXDigestArticles $getXDigestArticles,
        BuildXDigestText $buildXDigestText,
    ): int {
        $until = CarbonImmutable::now();
        $cutoff = $xDigestLogRepository->latestCutoff();
        $isBootstrap = $cutoff === null;
        $cutoff ??= $until;

        // 初回実行時はcutoff===untilとなり、GetXDigestArticlesの条件(> cutoff かつ <= until)を
        // 満たすレコードは存在しえないため、自然に対象0件の分岐へ合流する。
        $digest = $getXDigestArticles($cutoff, $until);

        if ($digest->totalCount === 0) {
            $xDigestLogRepository->create([
                'cutoff_at' => $until,
                'article_count' => 0,
                'status' => XDigestLogStatus::Success,
            ]);

            logger($isBootstrap
                ? '[XDailyDigestCommand] bootstrap run: cutoff initialized without posting'
                : '[XDailyDigestCommand] no articles to report, skip posting');

            return self::SUCCESS;
        }

        $text = $buildXDigestText($digest);

        $log = $xDigestLogRepository->create([
            'cutoff_at' => $until,
            'article_count' => $digest->totalCount,
            'status' => XDigestLogStatus::Pending,
        ]);

        try {
            $twitterV2Api = app(TwitterV2Api::class);
            $result = $twitterV2Api->post('tweets', ['text' => $text]);
            $httpCode = $twitterV2Api->getLastHttpCode();

            if ($httpCode < 200 || $httpCode >= 300) {
                throw new TwitterApiRequestException(sprintf('Twitter API request failed with status %d', $httpCode));
            }

            logger('[XDailyDigestCommand]', [$result]);
        } catch (Throwable $throwable) {
            report($throwable);
            $xDigestLogRepository->update($log, ['status' => XDigestLogStatus::Failed]);

            return self::FAILURE;
        }

        $xDigestLogRepository->update($log, ['status' => XDigestLogStatus::Success]);

        return self::SUCCESS;
    }
}
