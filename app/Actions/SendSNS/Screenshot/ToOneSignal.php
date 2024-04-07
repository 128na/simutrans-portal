<?php

declare(strict_types=1);

namespace App\Actions\SendSNS\Screenshot;

use App\Models\Screenshot;
use App\Notifications\SendArticlePublished;
use App\Notifications\SendArticleUpdated;
use App\Notifications\SendSNSNotification;
use Berkayk\OneSignal\OneSignalFacade;
use Exception;
use Throwable;

final readonly class ToOneSignal
{
    public function __construct(
        private GetScreenshotParam $getScreenshotParam,
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
