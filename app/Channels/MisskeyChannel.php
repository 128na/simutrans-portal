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
        private readonly MisskeyApiClient $misskeyApiClient,
        private readonly MessageGenerator $messageGenerator,
    ) {
    }

    public function send(Article $article, SendArticleNotification $sendArticleNotification): void
    {
        try {
            $text = $this->buildMessage($article, $sendArticleNotification);
            $result = $this->misskeyApiClient->send($text);
            logger('misskey', [$result]);
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

    public function featureEnabled(): bool
    {
        return (bool) config('services.misskey.token');
    }
}
