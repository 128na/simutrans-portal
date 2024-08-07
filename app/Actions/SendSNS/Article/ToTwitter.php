<?php

declare(strict_types=1);

namespace App\Actions\SendSNS\Article;

use App\Models\Article;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Notifications\SendSNSNotification;
use App\Services\Twitter\TwitterV2Api;
use Exception;
use Throwable;

final readonly class ToTwitter
{
    public function __construct(
        private TwitterV2Api $twitterV2Api,
        private GetArticleParam $getArticleParam,
    ) {}

    public function __invoke(Article $article, SendSNSNotification $sendSNSNotification): void
    {
        try {
            $data = ['text' => match (true) {
                $sendSNSNotification instanceof SendArticlePublished => $this->publish($article),
                $sendSNSNotification instanceof SendArticleUpdated => $this->update($article),
                default => throw new Exception(sprintf('unsupport notification "%s" provided', $sendSNSNotification::class)),
            }];
            $result = $this->twitterV2Api->post('tweets', $data);
            logger('[TwitterChannel]', [$result]);
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
