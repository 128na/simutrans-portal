<?php

namespace App\Actions\SendSNS\Screenshot;

use App\Models\Screenshot;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Notifications\SendSNSNotification;
use App\Services\Attachment\ConvertFailedException;
use App\Services\BlueSky\BlueSkyApiClient;
use Exception;
use potibm\Bluesky\Exception\HttpStatusCodeException;
use potibm\Bluesky\Feed\Post;
use Throwable;

class ToBluesky
{
    public function __construct(
        private readonly BlueSkyApiClient $blueSkyApiClient,
        private readonly GetScreenshotParam $getScreenshotParam,
    ) {
    }

    public function __invoke(Screenshot $screenshot, SendSNSNotification $sendSNSNotification): void
    {
        try {
            $post = match (true) {
                $sendSNSNotification instanceof SendArticlePublished => $this->publish($screenshot),
                $sendSNSNotification instanceof SendArticleUpdated => $this->update($screenshot),
                default => throw new Exception(sprintf('unsupport notification "%s" provided', $sendSNSNotification::class)),
            };
            $result = $this->blueSkyApiClient->send($post);
            logger('[BlueSkyChannel]', [$result->getUri()]);
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function publish(Screenshot $screenshot): Post
    {
        return $this->createPost($screenshot, __('notification.simple_article.create', ($this->getScreenshotParam)($screenshot)));
    }

    private function update(Screenshot $screenshot): Post
    {
        return $this->createPost($screenshot, __('notification.simple_article.update', ($this->getScreenshotParam)($screenshot)));
    }

    private function createPost(Screenshot $screenshot, string $text): Post
    {
        $post = Post::create($text);

        try {
            return $this->blueSkyApiClient->addWebsiteCardScreenshot($post, $screenshot);
        } catch (ConvertFailedException $e) {
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
