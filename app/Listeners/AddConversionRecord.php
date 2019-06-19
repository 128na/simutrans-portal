<?php
namespace App\Listeners;

use App\Events\ArticleConversion;

class AddConversionRecord
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
    public function handle(ArticleConversion $event)
    {
        $event->article->conversions()->create();
    }
}
