<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\Article\ArticleStored;
use App\Listeners\BaseListener;
use App\Notifications\SendArticlePublished;
use Illuminate\Log\Logger;

class OnArticleStored extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(ArticleStored $articleStored): void
    {
        $this->logger->channel('audit')->info('記事作成', $this->getArticleInfo($articleStored->article));
        if (!$articleStored->article->is_publish) {
            return;
        }
        if (!$articleStored->shouldNotify) {
            return;
        }
        $articleStored->article->notify(new SendArticlePublished());
    }
}
