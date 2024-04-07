<?php

namespace App\Actions\SendSNS\Screenshot;

use App\Models\Screenshot;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Notifications\SendSNSNotification;
use App\Services\Misskey\MisskeyApiClient;
use Exception;
use Throwable;

class ToMisskey
{
    public function __construct(
        private readonly MisskeyApiClient $misskeyApiClient,
        private readonly GetScreenshotParam $getScreenshotParam,
    ) {
    }

    public function __invoke(Screenshot $screenshot, SendSNSNotification $sendSNSNotification): void
    {
        try {
            $text = match (true) {
                $sendSNSNotification instanceof SendArticlePublished => $this->publish($screenshot),
                $sendSNSNotification instanceof SendArticleUpdated => $this->update($screenshot),
                default => throw new Exception(sprintf('unsupport notification "%s" provided', $sendSNSNotification::class)),
            };
            $result = $this->misskeyApiClient->send($text);
            logger('[MisskeyChannel]', [$result]);
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function publish(Screenshot $screenshot): string
    {
        return __('notification.screenshot.create', ($this->getScreenshotParam)($screenshot));
    }

    private function update(Screenshot $screenshot): string
    {
        return __('notification.screenshot.update', ($this->getScreenshotParam)($screenshot));
    }
}
