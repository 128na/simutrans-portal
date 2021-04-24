<?php

namespace App\Console\Commands;

use App\Jobs\Article\JobCheckDeadLink;
use Illuminate\Console\Command;

class CommandCheckDeadLink extends Command
{
    protected $signature = 'check:deadlink';

    protected $description = '公開済みのアドオン紹介記事でリンク切れのものを確認する。リンク切れのものはステータスを非公開にする';

    public function handle()
    {
        JobCheckDeadLink::dispatchSync();

        return 0;
    }
}
