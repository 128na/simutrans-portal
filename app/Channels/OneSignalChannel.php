<?php

declare(strict_types=1);

namespace App\Channels;

use App\Models\Article;
use App\Notifications\ArticleNotification;
use App\Notifications\ArticlePublished;
use App\Notifications\ArticleUpdated;
use App\Services\Notification\MessageGenerator;
use Berkayk\OneSignal\OneSignalFacade;
use Exception;
use Throwable;

class OneSignalChannel extends BaseChannel
{
    public function __construct(
        private MessageGenerator $messageGenerator
    ) {
    }

    public function send(Article $notifiable, ArticleNotification $notification): void
    {
        try {
            OneSignalFacade::sendNotificationToAll(
                $this->buildMessage($notifiable, $notification),
                route('articles.show', $notifiable->slug),
            );
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function buildMessage(Article $notifiable, ArticleNotification $notification): string
    {
        return match (true) {
            $notification instanceof ArticlePublished => $this->messageGenerator->buildSimplePublishedMessage($notifiable),
            $notification instanceof ArticleUpdated => $this->messageGenerator->buildSimpleUpdatedMessage($notifiable),
            default => throw new Exception(sprintf('unsupport notification "%s" provided', get_class($notification))),
        };
    }

    public static function featureEnabled(): bool
    {
        return config('onesignal.app_id') && config('onesignal.rest_api_key');
    }
}
