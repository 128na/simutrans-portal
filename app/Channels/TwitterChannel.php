<?php

declare(strict_types=1);

namespace App\Channels;

use App\Models\Article;
use App\Notifications\SendArticleNotification;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Services\Notification\MessageGenerator;
use App\Services\Twitter\TwitterV2Api;
use Exception;
use Throwable;

class TwitterChannel extends BaseChannel
{
    public function __construct(
        private readonly TwitterV2Api $twitterV2Api,
        private readonly MessageGenerator $messageGenerator
    ) {
    }

    public function send(Article $notifiable, SendArticleNotification $notification): void
    {
        try {
            $data = ['text' => $this->buildMessage($notifiable, $notification)];
            $result = $this->twitterV2Api->post('tweets', $data, true);
            logger('tweet', [$result]);
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function buildMessage(Article $notifiable, SendArticleNotification $notification): string
    {
        return match (true) {
            $notification instanceof SendArticlePublished => $this->messageGenerator->buildPublishedMessage($notifiable),
            $notification instanceof SendArticleUpdated => $this->messageGenerator->buildUpdatedMessage($notifiable),
            default => throw new Exception(sprintf('unsupport notification "%s" provided', $notification::class)),
        };
    }

    public static function featureEnabled(): bool
    {
        return (bool) config('services.twitter.client_id');
    }
}
