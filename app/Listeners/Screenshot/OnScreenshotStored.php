<?php

declare(strict_types=1);

namespace App\Listeners\Screenshot;

use App\Enums\ScreenshotStatus;
use App\Events\Screenshot\ScreenshotStored;
use App\Notifications\SendScreenshotPublished;
use Illuminate\Log\Logger;

final readonly class OnScreenshotStored
{
    public function __construct(private readonly Logger $logger) {}

    public function handle(ScreenshotStored $screenshotStored): void
    {
        $this->logger->channel('audit')->info('スクリーンショット作成', $screenshotStored->screenshot->getInfoLogging());
        if ($screenshotStored->screenshot->status !== ScreenshotStatus::Publish) {
            return;
        }

        if (! $screenshotStored->shouldNotify) {
            return;
        }

        $screenshotStored->screenshot->notify(new SendScreenshotPublished);
    }
}
