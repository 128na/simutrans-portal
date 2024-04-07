<?php

namespace App\Actions\SendSNS\Screenshot;

use App\Actions\SendSNS\GetScreenshotParam;
use App\Models\Screenshot;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Notifications\SendSNSNotification;
use Berkayk\OneSignal\OneSignalFacade;
use Exception;
use Throwable;

class ToOneSignal
{
    public function __construct(
        private readonly GetScreenshotParam $getScreenshotParam,
    ) {
    }

    public function __invoke(Screenshot $screenshot, SendSNSNotification $sendSNSNotification): void
    {
        try {
            OneSignalFacade::sendNotificationToAll(
                match (true) {
                    $sendSNSNotification instanceof SendArticlePublished => $this->publish($screenshot),
                    $sendSNSNotification instanceof SendArticleUpdated => $this->update($screenshot),
                    default => throw new Exception(sprintf('unsupport notification "%s" provided', $sendSNSNotification::class)),
                },
                route('screenshots.show', $screenshot)
            );
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    private function publish(Screenshot $screenshot): string
    {
        return __('notification.simple_screenshot.create', ($this->getScreenshotParam)($screenshot));
    }

    private function update(Screenshot $screenshot): string
    {
        return __('notification.simple_screenshot.update', ($this->getScreenshotParam)($screenshot));
    }
}
