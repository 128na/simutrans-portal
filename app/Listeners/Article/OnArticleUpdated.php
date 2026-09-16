<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\Article\ArticleUpdated;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use Illuminate\Log\Logger;

class OnArticleUpdated
{
    public function __construct(private Logger $logger) {}

    public function handle(ArticleUpdated $articleUpdated): void
    {
        $this->logger->channel('audit')->info('記事更新', $articleUpdated->article->getInfoLogging());

        // 公開以外
        if (! $articleUpdated->article->is_publish) {
            return;
        }

        // 通知を希望しない
        if (! $articleUpdated->shouldNotify) {
            return;
        }

        // published_atがnullから初めて変わった場合は新規投稿扱い
        // Xデイリー集約投稿(sns:x-daily-digest)の対象判定に使う日時も、
        // is_publish/shouldNotifyの両ゲートを通過した通知確定時点でのみ刻む。
        if ($articleUpdated->notYetPublished) {
            $articleUpdated->article->notify(new SendArticlePublished);
            $articleUpdated->article->forceFill(['sns_digest_published_at' => now()])->saveQuietly();
        } else {
            $articleUpdated->article->notify(new SendArticleUpdated);
            $articleUpdated->article->forceFill(['sns_digest_updated_at' => now()])->saveQuietly();
        }
    }
}
