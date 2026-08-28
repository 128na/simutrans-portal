<?php

declare(strict_types=1);

namespace App\Console\Commands\Backup;

use App\Actions\Backup\SyncUploadsDigestTracker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReportSyncUploadsDigestCommand extends Command
{
    protected $signature = 'backup:report-sync-uploads-digest';

    protected $description = 'ユーザーアップロード同期(backup:sync-uploads)の本日分の転送件数・エラー件数をDiscordへ通知する';

    public function handle(SyncUploadsDigestTracker $digest): int
    {
        $transferred = $digest->transferredToday();
        $errors = $digest->errorsToday();

        if ($transferred === 0 && $errors === 0) {
            return Command::SUCCESS;
        }

        Log::channel('discord_backup')->info(sprintf(
            'ユーザーアップロード同期(本日分): %d件転送, %dエラー',
            $transferred,
            $errors
        ));

        return Command::SUCCESS;
    }
}
