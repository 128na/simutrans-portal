<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\Article\ArticleUpdated;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use Illuminate\Log\Logger;

final readonly class OnArticleUpdated
{
    public function __construct(private Logger $logger) {}

    public function handle(ArticleUpdated $articleUpdated): void
    {
        $this->logger->channel('audit')->info('記事更新', $articleUpdated->article->getInfoLogging());

        // 公開以外
        if (! $articleUpdated->article->is_publish) {
            dump('記事公開ではないため通知しない');
            return;
        }

        // 通知を希望しない
        if (! $articleUpdated->shouldNotify) {
            dump('通知しない設定のため通知しない');
            return;
        }

        // published_atがnullから初めて変わった場合は新規投稿扱い
        if ($articleUpdated->notYetPublished) {
            dump('記事が未公開から公開になったため公開通知を送信する');
            $articleUpdated->article->notify(new SendArticlePublished);
        } else {
            dump('記事更新通知を送信する');
            $articleUpdated->article->notify(new SendArticleUpdated);
        }
    }
}
