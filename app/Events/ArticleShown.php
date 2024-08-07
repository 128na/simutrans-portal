<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Article;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * 記事閲覧時に閲覧をカウントする.
 */
final class ArticleShown
{
    use Dispatchable;
    use SerializesModels;

    /**
     * 新しいイベントインスタンスの生成.
     */
    public function __construct(public Article $article) {}
}
