<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\Article\ArticleStored;
use App\Listeners\BaseListener;
use App\Notifications\SendArticlePublished;
use Illuminate\Log\Logger;

class OnArticleStored extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(ArticleStored $event): void
    {
        $this->logger->channel('audit')->info('記事作成', $this->getArticleInfo($event->article));

        if ($event->article->is_publish && $event->shouldNotify) {
            $event->article->notify(new SendArticlePublished());
        }
    }
}
