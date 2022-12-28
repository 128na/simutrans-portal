<?php

namespace App\Events;

use App\Models\Article;
use Illuminate\Queue\SerializesModels;

/**
 * 記事閲覧時に閲覧をカウントする.
 */
class ArticleShown
{
    use SerializesModels;

    /**
     * 新しいイベントインスタンスの生成.
     */
    public function __construct(public Article $article)
    {
    }
}
