<?php

declare(strict_types=1);

namespace App\Console\Commands\Sns;

use App\Actions\SendSNS\Article\BuildXDigestText;
use App\Actions\SendSNS\Article\GetXDigestArticles;
use App\Repositories\XDigestLogRepository;
use App\Services\Twitter\Exceptions\TwitterApiRequestException;
use App\Services\Twitter\TwitterV2Api;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Throwable;

/**
 * X (Twitter) への新規・更新記事の通知を、1日2回(12:00/21:30 JST)の集約投稿へまとめて送る。
 * 前回の成功実行(x_digest_logs.cutoff_at)以降の公開・更新記事を対象とし、成功時のみ
 * cutoff を進める。対象0件または投稿失敗時はXへ投稿しない(ADR-0005)。
 */
class XDailyDigestCommand extends Command
{
    protected $signature = 'sns:x-daily-digest';

    protected $description = '前回の成功配信以降の新規・更新記事をXへ1日2回集約投稿する';

    public function handle(
        XDigestLogRepository $xDigestLogRepository,
        GetXDigestArticles $getXDigestArticles,
        BuildXDigestText $buildXDigestText,
        TwitterV2Api $twitterV2Api,
    ): int {
        $until = CarbonImmutable::now();
        $cutoff = $xDigestLogRepository->latestCutoff();

        if ($cutoff === null) {
            // 初回実行: 過去の全記事を遡って通知すると大量投稿になるため、
            // 今回のuntilを起点として記録するのみで投稿はしない。
            $xDigestLogRepository->create(['cutoff_at' => $until, 'article_count' => 0]);
            logger('[XDailyDigestCommand] bootstrap run: cutoff initialized without posting');

            return self::SUCCESS;
        }

        $digest = $getXDigestArticles($cutoff, $until);

        if ($digest->totalCount === 0) {
            $xDigestLogRepository->create(['cutoff_at' => $until, 'article_count' => 0]);
            logger('[XDailyDigestCommand] no articles to report, skip posting');

            return self::SUCCESS;
        }

        $text = $buildXDigestText($digest);

        try {
            $result = $twitterV2Api->post('tweets', ['text' => $text]);
            $httpCode = $twitterV2Api->getLastHttpCode();

            if ($httpCode < 200 || $httpCode >= 300) {
                throw new TwitterApiRequestException(sprintf('Twitter API request failed with status %d', $httpCode));
            }

            logger('[XDailyDigestCommand]', [$result]);
        } catch (Throwable $throwable) {
            report($throwable);

            return self::FAILURE;
        }

        $xDigestLogRepository->create(['cutoff_at' => $until, 'article_count' => $digest->totalCount]);

        return self::SUCCESS;
    }
}
