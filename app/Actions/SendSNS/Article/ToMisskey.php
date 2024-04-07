<?php

namespace App\Actions\SendSNS\Article;

use App\Actions\SendSNS\GetArticleParam;
use App\Models\Article;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Notifications\SendSNSNotification;
use App\Services\Misskey\MisskeyApiClient;
use Exception;
use Throwable;

class ToMisskey
{
    public function __construct(
        private readonly MisskeyApiClient $misskeyApiClient,
        private readonly GetArticleParam $getArticleParam,
    ) {
    }

    public function __invoke(Article $article, SendSNSNotification $sendSNSNotification): void
    {
        match (true) {
            $sendSNSNotification instanceof SendArticlePublished => $this->publish($article),
            $sendSNSNotification instanceof SendArticleUpdated => $this->update($article),
            default => throw new Exception(sprintf('unsupport notification "%s" provided', $sendSNSNotification::class)),
        };
    }

    private function publish(Article $article): void
    {
        try {
            $text = __('notification.article.create', ($this->getArticleParam)($article));
            $result = $this->misskeyApiClient->send($text);
            logger('[MisskeyChannel]', [$result]);
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function update(Article $article): void
    {
        try {
            $text = __('notification.article.update', ($this->getArticleParam)($article));
            $result = $this->misskeyApiClient->send($text);
            logger('[MisskeyChannel]', [$result]);
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }
}
