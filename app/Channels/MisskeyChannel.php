<?php

declare(strict_types=1);

namespace App\Channels;

use App\Models\Article;
use App\Notifications\ArticleNotification;
use App\Notifications\ArticlePublished;
use App\Notifications\ArticleUpdated;
use App\Services\Misskey\MisskeyApiClient;
use App\Services\Notification\MessageGenerator;
use Exception;
use Throwable;

class MisskeyChannel
{
    public function __construct(
        private MisskeyApiClient $misskeyApiClient,
        private MessageGenerator $messageGenerator,
    ) {
    }

    public function send(Article $notifiable, ArticleNotification $notification): void
    {
        try {
            $text = $this->buildMessage($notifiable, $notification);
            $result = $this->misskeyApiClient->send($text);
            logger('misskey', [$result]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function buildMessage(Article $notifiable, ArticleNotification $notification): string
    {
        return match (true) {
            $notification instanceof ArticlePublished => $this->messageGenerator->buildPublishedMessage($notifiable),
            $notification instanceof ArticleUpdated => $this->messageGenerator->buildUpdatedMessage($notifiable),
            default => throw new Exception(sprintf('unsupport notification "%s" provided', get_class($notification))),
        };
    }
}
