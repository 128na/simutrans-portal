<?php

declare(strict_types=1);

namespace App\Console\Commands\Backup;

use App\Actions\Backup\SyncUserUploadsToDropbox;
use Illuminate\Console\Command;
use Throwable;

class SyncUserUploadsCommand extends Command
{
    protected $signature = 'backup:sync-uploads';

    protected $description = 'ユーザーアップロードファイル(storage/app/public/user)をrclone経由でDropboxへ差分同期する';

    public function handle(SyncUserUploadsToDropbox $syncUserUploadsToDropbox): int
    {
        try {
            $syncUserUploadsToDropbox();
        } catch (Throwable $throwable) {
            report($throwable);
            $this->error($throwable->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
