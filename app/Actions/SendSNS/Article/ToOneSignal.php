<?php

declare(strict_types=1);

namespace App\Actions\SendSNS\Article;

use App\Models\Article;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Notifications\SendSNSNotification;
use Berkayk\OneSignal\OneSignalFacade;
use Exception;
use Throwable;

final readonly class ToOneSignal
{
    public function __construct(
        private GetArticleParam $getArticleParam,
    ) {}

    public function __invoke(Article $article, SendSNSNotification $sendSNSNotification): void
    {
        try {
            OneSignalFacade::sendNotificationToAll(
                match (true) {
                    $sendSNSNotification instanceof SendArticlePublished => $this->publish($article),
                    $sendSNSNotification instanceof SendArticleUpdated => $this->update($article),
                    default => throw new Exception(sprintf(
                        'unsupport notification "%s" provided',
                        $sendSNSNotification::class,
                    )),
                },
                route('articles.show', [
                    'userIdOrNickname' => $article->user?->nickname ?? $article->user_id,
                    'articleSlug' => $article->slug,
                ]),
            );
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function publish(Article $article): string
    {
        return __('notification.simple_article.create', ($this->getArticleParam)($article));
    }

    private function update(Article $article): string
    {
        return __('notification.simple_article.update', ($this->getArticleParam)($article));
    }
}
