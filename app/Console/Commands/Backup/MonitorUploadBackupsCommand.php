<?php

declare(strict_types=1);

namespace App\Console\Commands\Backup;

use App\Actions\Backup\UploadBackupHealth;
use Illuminate\Console\Command;

class MonitorUploadBackupsCommand extends Command
{
    protected $signature = 'backup:monitor-uploads';

    protected $description = 'アップロード同期が保存先ごとに6時間以上完了していない場合に通知する';

    public function handle(UploadBackupHealth $health): int
    {
        return $health->check() === [] ? Command::SUCCESS : Command::FAILURE;
    }
}
