<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\Article\JobCheckDeadLink;
use Illuminate\Console\Command;
use Throwable;

class CommandCheckDeadLink extends Command
{
    protected $signature = 'check:deadlink';

    protected $description = '公開済みのアドオン紹介記事でリンク切れのものを確認する。リンク切れのものはステータスを非公開にする';

    public function handle(): int
    {
        try {
            JobCheckDeadLink::dispatchSync();
        } catch (Throwable $throwable) {
            report($throwable);

            return 1;
        }

        return 0;
    }
}
