<?php

namespace App\Console\Commands;

use App\Jobs\Article\JobCheckDeadLink;
use Illuminate\Console\Command;

/**
 * 公開済みのアドオン紹介記事でリンク切れのものを確認する
 * リンク切れのものはステータスを非公開にする.
 */
class CheckDeadLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:deadlink';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Dead Link';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch_now(app(JobCheckDeadLink::class));

        return 0;
    }
}
