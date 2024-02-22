<?php

declare(strict_types=1);

namespace App\Listeners\Article;

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

    public function handle(ArticleUpdated $articleUpdated): void
    {
        $this->logger->channel('audit')->info('記事更新', $this->getArticleInfo($articleUpdated->article));

        // 公開以外
        if (! $articleUpdated->article->is_publish) {
            return;
        }

        // 通知を希望しない
        if (! $articleUpdated->shouldNotify) {
            return;
        }

        // 更新日を更新しない
        if ($articleUpdated->withoutUpdateModifiedAt) {
            return;
        }

        // published_atがnullから初めて変わった場合は新規投稿扱い
        if ($articleUpdated->notYetPublished) {
            $articleUpdated->article->notify(new SendArticlePublished());
        } else {
            $articleUpdated->article->notify(new SendArticleUpdated());
        }
    }
}
