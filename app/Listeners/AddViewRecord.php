<?php

namespace App\Listeners;

use App\Events\ArticleShown;
use App\Models\ViewCount;

class AddViewRecord
{
    /**
     * イベントリスナ生成.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * イベントの処理.
     *
     * @return void
     */
    public function handle(ArticleShown $event)
    {
        ViewCount::countUp($event->article);
    }
}
