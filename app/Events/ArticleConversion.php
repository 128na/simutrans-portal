<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Article;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * DL、掲載先遷移時にコンバージョンをカウントする.
 */
final class ArticleConversion
{
    use Dispatchable;
    use SerializesModels;

    /**
     * 新しいイベントインスタンスの生成.
     */
    public function __construct(public Article $article)
    {
    }
}
