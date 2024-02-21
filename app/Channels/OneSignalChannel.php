<?php

declare(strict_types=1);

namespace App\Channels;

use App\Models\Article;
use App\Notifications\SendArticleNotification;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Services\Notification\MessageGenerator;
use Berkayk\OneSignal\OneSignalFacade;
use Exception;
use Throwable;

class OneSignalChannel extends BaseChannel
{
    public function __construct(
        private readonly MessageGenerator $messageGenerator
    ) {
    }

    public function send(Article $article, SendArticleNotification $sendArticleNotification): void
    {
        try {
            OneSignalFacade::sendNotificationToAll(
                $this->buildMessage($article, $sendArticleNotification),
                route('articles.show', ['userIdOrNickname' => $article->user?->nickname ?? $article->user_id, 'articleSlug' => $article->slug]),
            );
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function buildMessage(Article $article, SendArticleNotification $sendArticleNotification): string
    {
        return match (true) {
            $sendArticleNotification instanceof SendArticlePublished => $this->messageGenerator->buildSimplePublishedMessage($article),
            $sendArticleNotification instanceof SendArticleUpdated => $this->messageGenerator->buildSimpleUpdatedMessage($article),
            default => throw new Exception(sprintf('unsupport notification "%s" provided', $sendArticleNotification::class)),
        };
    }

    public static function featureEnabled(): bool
    {
        return config('onesignal.app_id') && config('onesignal.rest_api_key');
    }
}
