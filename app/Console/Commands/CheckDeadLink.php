<?php

namespace App\Console\Commands;

use App\Notifications\DeadLinkDetected;
use App\Services\CheckDeadLinkService;
use Illuminate\Console\Command;

/**
 * 公開済みのアドオン紹介記事でリンク切れのものを確認する
 * リンク切れのものはステータスを非公開にする
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

    private CheckDeadLinkService $check_deadlink_service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CheckDeadLinkService $check_deadlink_service)
    {
        parent::__construct();
        $this->check_deadlink_service = $check_deadlink_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = $this->check_deadlink_service->getTargetArticles()->map(function ($article) {
            if ($this->check_deadlink_service->isLinkDead($article)) {
                $this->info('dead link ' . $article->title);
                logger('dead link ' . $article->title);

                $article->update(['status' => config('status.private')]);
                $article->notify(new DeadLinkDetected);
            }
        })->count();
        logger("$count articles checked");

        return 0;
    }
}
