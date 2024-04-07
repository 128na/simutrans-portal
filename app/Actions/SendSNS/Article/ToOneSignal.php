<?php

namespace App\Actions\SendSNS\Article;

use App\Actions\SendSNS\GetArticleParam;
use App\Models\Article;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Notifications\SendSNSNotification;
use Berkayk\OneSignal\OneSignalFacade;
use Exception;
use Throwable;

class ToOneSignal
{
    public function __construct(
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
            OneSignalFacade::sendNotificationToAll(
                __('notification.simple_article.create', ($this->getArticleParam)($article)),
                route('articles.show', ['userIdOrNickname' => $article->user?->nickname ?? $article->user_id, 'articleSlug' => $article->slug]),
            );
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function update(Article $article): void
    {
        try {
            OneSignalFacade::sendNotificationToAll(
                __('notification.simple_article.update', ($this->getArticleParam)($article)),
                route('articles.show', ['userIdOrNickname' => $article->user?->nickname ?? $article->user_id, 'articleSlug' => $article->slug]),
            );
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }
}
