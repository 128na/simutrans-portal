<?php

declare(strict_types=1);

namespace App\Channels;

use App\Models\Article;
use App\Notifications\SendArticleNotification;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Services\Attachment\ConvertFailedException;
use App\Services\BlueSky\BlueSkyApiClient;
use App\Services\Notification\MessageGenerator;
use Exception;
use potibm\Bluesky\Exception\HttpStatusCodeException;
use potibm\Bluesky\Feed\Post;
use Throwable;

class BlueSkyChannel extends BaseChannel
{
    public function __construct(
        private readonly BlueSkyApiClient $blueSkyApiClient,
        private readonly MessageGenerator $messageGenerator,
    ) {
    }

    public function send(Article $notifiable, SendArticleNotification $notification): void
    {
        try {
            $post = $this->buildMessage($notifiable, $notification);
            $result = $this->blueSkyApiClient->send($post);
            logger('blueSky', [$result->getUri()]);
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function buildMessage(Article $notifiable, SendArticleNotification $notification): Post
    {
        $text = match (true) {
            $notification instanceof SendArticlePublished => $this->messageGenerator->buildSimplePublishedMessage($notifiable),
            $notification instanceof SendArticleUpdated => $this->messageGenerator->buildSimpleUpdatedMessage($notifiable),
            default => throw new Exception(sprintf('unsupport notification "%s" provided', $notification::class)),
        };
        $post = Post::create($text);

        try {
            return $this->blueSkyApiClient->addWebsiteCard($post, $notifiable);
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

    public static function featureEnabled(): bool
    {
        return (bool) config('services.bluesky.user');
    }
}
