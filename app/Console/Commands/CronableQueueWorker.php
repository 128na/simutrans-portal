<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

final class CronableQueueWorker extends Command
{
    protected $signature = 'queue:cron';

    protected $description = '指定時間だけワーカプロセスを起動する';

    public function handle(): int
    {
        logger()->channel('worker')->info('start');
        $this->call('queue:work', [
            '--max-time' => 295,
            '--sleep' => 30,
        ]);
        logger()->channel('worker')->info('end');

        return 0;
    }
}
