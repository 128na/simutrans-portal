<?php

declare(strict_types=1);

namespace App\Actions\SendSNS\Article;

use App\Models\Article;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Notifications\SendSNSNotification;
use App\Services\BlueSky\BlueSkyApiClient;
use App\Services\BlueSky\ResizeFailedException;
use Exception;
use potibm\Bluesky\Exception\HttpStatusCodeException;
use potibm\Bluesky\Feed\Post;
use Throwable;

final readonly class ToBluesky
{
    public function __construct(
        private BlueSkyApiClient $blueSkyApiClient,
        private GetArticleParam $getArticleParam,
    ) {}

    public function __invoke(Article $article, SendSNSNotification $sendSNSNotification): void
    {
        try {
            $post = match (true) {
                $sendSNSNotification instanceof SendArticlePublished => $this->publish($article),
                $sendSNSNotification instanceof SendArticleUpdated => $this->update($article),
                default => throw new Exception(sprintf('unsupport notification "%s" provided', $sendSNSNotification::class)),
            };
            $result = $this->blueSkyApiClient->send($post);
            logger('[BlueSkyChannel]', [$result->getUri()]);
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function publish(Article $article): Post
    {
        return $this->createPost($article, __('notification.simple_article.create', ($this->getArticleParam)($article)));
    }

    private function update(Article $article): Post
    {
        return $this->createPost($article, __('notification.simple_article.update', ($this->getArticleParam)($article)));
    }

    private function createPost(Article $article, string $text): Post
    {
        $post = Post::create($text);

        try {
            return $this->blueSkyApiClient->addWebsiteCard($post, $article);
        } catch (ResizeFailedException $e) {
            report($e);
        } catch (HttpStatusCodeException $e) {
            // 画像が1MB以上だとエラーになる
            if (! str_contains($e->getMessage(), 'BlobTooLarge')) {
                report($e);
            }
        }

        return $post;
    }
}
