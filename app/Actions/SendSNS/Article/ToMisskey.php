<?php

declare(strict_types=1);

namespace App\Actions\SendSNS\Article;

use App\Models\Article;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Notifications\SendSNSNotification;
use App\Services\Misskey\MisskeyApiClient;
use Exception;
use Throwable;

final readonly class ToMisskey
{
    public function __construct(
        private MisskeyApiClient $misskeyApiClient,
        private GetArticleParam $getArticleParam,
    ) {}

    public function __invoke(Article $article, SendSNSNotification $sendSNSNotification): void
    {
        try {
            $text = match (true) {
                $sendSNSNotification instanceof SendArticlePublished => $this->publish($article),
                $sendSNSNotification instanceof SendArticleUpdated => $this->update($article),
                default => throw new Exception(sprintf('unsupport notification "%s" provided', $sendSNSNotification::class)),
            };
            $result = $this->misskeyApiClient->send($text);
            logger('[MisskeyChannel]', [$result]);
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function publish(Article $article): string
    {
        return __('notification.article.create', ($this->getArticleParam)($article));
    }

    private function update(Article $article): string
    {
        return __('notification.article.update', ($this->getArticleParam)($article));
    }
}
