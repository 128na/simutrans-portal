<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\Article\ArticleUpdated;
use App\Listeners\BaseListener;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use Illuminate\Log\Logger;

class OnArticleUpdated extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(ArticleUpdated $event): void
    {
        $this->logger->channel('audit')->info('記事更新', $this->getArticleInfo($event->article));

        // 公開以外
        if (! $event->article->is_publish) {
            return;
        }
        // 通知を希望しない
        if (! $event->shouldNotify) {
            return;
        }
        // 更新日を更新しない
        if ($event->withoutUpdateModifiedAt) {
            return;
        }

        // published_atがnullから初めて変わった場合は新規投稿扱い
        if ($event->notYetPublished) {
            $event->article->notify(new SendArticlePublished());
        } else {
            $event->article->notify(new SendArticleUpdated());
        }
    }
}
