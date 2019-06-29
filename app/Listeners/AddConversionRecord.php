<?php
namespace App\Listeners;

use App\Models\ConversionCount;
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
        ConversionCount::countUp($event->article->id);
    }
}
