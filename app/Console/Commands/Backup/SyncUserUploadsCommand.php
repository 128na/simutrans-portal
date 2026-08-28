<?php

declare(strict_types=1);

namespace App\Console\Commands\Backup;

use App\Actions\Backup\SyncUploadsDigestTracker;
use App\Actions\Backup\SyncUserUploads;
use Illuminate\Console\Command;
use Throwable;

class SyncUserUploadsCommand extends Command
{
    protected $signature = 'backup:sync-uploads';

    protected $description = 'ユーザーアップロードファイル(storage/app/public/user)をrclone経由でDropbox・ローカルへ差分同期する';

    public function handle(SyncUserUploads $syncUserUploads, SyncUploadsDigestTracker $digest): int
    {
        try {
            $digest->recordTransferred($syncUserUploads());
        } catch (Throwable $throwable) {
            $digest->recordError();
            report($throwable);
            $this->error($throwable->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
