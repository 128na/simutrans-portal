<?php
namespace App\Listeners;

use App\Events\ArticleShown;

class AddViewRecord
{
    /**
     * イベントリスナ生成
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * イベントの処理
     *
     * @return void
     */
    public function handle(ArticleShown $event)
    {
        $event->article->views()->create();
    }
}
