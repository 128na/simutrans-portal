<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\Article\ArticleUpdated;
use App\Listeners\BaseListener;
use Illuminate\Log\Logger;

class OnArticleUpdated extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(ArticleUpdated $event): void
    {
        $this->logger->channel('audit')->info('記事更新', $this->getArticleInfo($event->article));
    }
}
