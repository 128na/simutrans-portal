<?php

declare(strict_types=1);

namespace App\Channels;

use App\Models\Article;
use App\Notifications\SendArticleNotification;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Services\Misskey\MisskeyApiClient;
use App\Services\Notification\MessageGenerator;
use Exception;
use Throwable;

class MisskeyChannel extends BaseChannel
{
    public function __construct(
        private MisskeyApiClient $misskeyApiClient,
        private MessageGenerator $messageGenerator,
    ) {
    }

    public function send(Article $notifiable, SendArticleNotification $notification): void
    {
        try {
            $text = $this->buildMessage($notifiable, $notification);
            $result = $this->misskeyApiClient->send($text);
            logger('misskey', [$result]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function buildMessage(Article $notifiable, SendArticleNotification $notification): string
    {
        return match (true) {
            $notification instanceof SendArticlePublished => $this->messageGenerator->buildPublishedMessage($notifiable),
            $notification instanceof SendArticleUpdated => $this->messageGenerator->buildUpdatedMessage($notifiable),
            default => throw new Exception(sprintf('unsupport notification "%s" provided', get_class($notification))),
        };
    }

    public static function featureEnabled(): bool
    {
        return (bool) config('services.misskey.token');
    }
}
