<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\Article\ArticleDeleted;
use Illuminate\Log\Logger;

class OnArticleDeleted
{
    public function __construct(private Logger $logger) {}

    public function handle(ArticleDeleted $articleDeleted): void
    {
        $this->logger->channel('audit')->info('記事削除', $articleDeleted->article->getInfoLogging());
    }
}
