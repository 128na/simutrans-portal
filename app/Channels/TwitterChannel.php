<?php

declare(strict_types=1);

namespace App\Channels;

use App\Models\Article;
use App\Notifications\ArticleNotification;
use App\Notifications\ArticlePublished;
use App\Notifications\ArticleUpdated;
use App\Services\Notification\MessageGenerator;
use App\Services\Twitter\TwitterV2Api;
use Exception;
use Throwable;

class TwitterChannel
{
    public function __construct(
        private TwitterV2Api $twitterV2Api,
        private MessageGenerator $messageGenerator
    ) {
    }

    public function send(Article $notifiable, ArticleNotification $notification): void
    {
        try {
            $data = ['text' => $this->buildMessage($notifiable, $notification)];
            $result = $this->twitterV2Api->post('tweets', $data, true);
            logger('tweet result', [$result]);
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
