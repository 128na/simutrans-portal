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

    public function send(Article $article, SendArticleNotification $sendArticleNotification): void
    {
        try {
            $data = ['text' => $this->buildMessage($article, $sendArticleNotification)];
            $result = $this->twitterV2Api->post('tweets', $data);
            logger('tweet', [$result]);
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function buildMessage(Article $article, SendArticleNotification $sendArticleNotification): string
    {
        return match (true) {
            $sendArticleNotification instanceof SendArticlePublished => $this->messageGenerator->buildPublishedMessage($article),
            $sendArticleNotification instanceof SendArticleUpdated => $this->messageGenerator->buildUpdatedMessage($article),
            default => throw new Exception(sprintf('unsupport notification "%s" provided', $sendArticleNotification::class)),
        };
    }

    public static function featureEnabled(): bool
    {
        return (bool) config('services.twitter.client_id');
    }
}
