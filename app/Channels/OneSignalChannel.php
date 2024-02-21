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

    public function send(Article $notifiable, SendArticleNotification $notification): void
    {
        try {
            OneSignalFacade::sendNotificationToAll(
                $this->buildMessage($notifiable, $notification),
                route('articles.show', ['userIdOrNickname' => $notifiable->user?->nickname ?? $notifiable->user_id, 'articleSlug' => $notifiable->slug]),
            );
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function buildMessage(Article $notifiable, SendArticleNotification $notification): string
    {
        return match (true) {
            $notification instanceof SendArticlePublished => $this->messageGenerator->buildSimplePublishedMessage($notifiable),
            $notification instanceof SendArticleUpdated => $this->messageGenerator->buildSimpleUpdatedMessage($notifiable),
            default => throw new Exception(sprintf('unsupport notification "%s" provided', $notification::class)),
        };
    }

    public static function featureEnabled(): bool
    {
        return config('onesignal.app_id') && config('onesignal.rest_api_key');
    }
}
