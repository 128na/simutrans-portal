<?php

namespace App\Events;

use App\Models\Article;
use Illuminate\Queue\SerializesModels;

/**
 * DL、掲載先遷移時にコンバージョンをカウントする.
 */
class ArticleConversion
{
    use SerializesModels;

    public $article;

    /**
     * 新しいイベントインスタンスの生成.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }
}
