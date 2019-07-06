<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use Notification;
use App\Notifications\DeadLinkDetected;

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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Article::where('post_type', 'addon-introduction')->with('user')->cursor() as $article) {
            $link = $article->getContents('link');

            if(!self::isStatusOK($link)) {
                $this->info('dead link '.$link);
                logger('dead link '.$link);
                $article->status = config('status.private');
                $article->save();

                Notification::send($article->user, new DeadLinkDetected($article));
            }
        }
    }

    private static function isStatusOK($url)
    {
        $info_list = @get_headers($url) ?: [];
        foreach ($info_list as $info) {
            if(stripos($info, ' 200 OK') !== false) {
                return true;
            }
        }
        return false;
    }
}
