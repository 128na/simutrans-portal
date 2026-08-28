<?php

declare(strict_types=1);

namespace App\Console\Commands\Backup;

use App\Actions\Backup\SyncUploadsDigestTracker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReportSyncUploadsDigestCommand extends Command
{
    protected $signature = 'backup:report-sync-uploads-digest';

    protected $description = 'ユーザーアップロード同期(backup:sync-uploads)の前日分の転送件数・エラー件数をDiscordへ通知する';

    public function handle(SyncUploadsDigestTracker $digest): int
    {
        // 日付境界をまたぐ実行(backup:sync-uploadsは2分周期で走り続ける)を確実に拾うため、
        // 日付が変わった直後に前日分を集計する。当日中に集計すると、集計時刻以降に
        // 積み上がった分が誰にも読まれず消えてしまう。
        $date = now()->subDay()->toDateString();

        $transferred = $digest->transferredOn($date);
        $errors = $digest->errorsOn($date);

        if ($transferred === 0 && $errors === 0) {
            return Command::SUCCESS;
        }

        Log::channel('discord_backup')->info(sprintf(
            'ユーザーアップロード同期(%s分): %d件転送, %dエラー',
            $date,
            $transferred,
            $errors
        ));

        return Command::SUCCESS;
    }
}
