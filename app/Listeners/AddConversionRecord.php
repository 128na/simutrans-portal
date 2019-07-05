<?php
namespace App\Listeners;

use App\Events\ArticleConversion;
use App\Models\ConversionCount;

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
        ConversionCount::countUp($event->article);
    }
}
