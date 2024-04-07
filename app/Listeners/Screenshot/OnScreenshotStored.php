<?php

declare(strict_types=1);

namespace App\Listeners\Screenshot;

use App\Events\Screenshot\ScreenshotStored;
use App\Listeners\BaseListener;
use App\Notifications\SendScreenshotPublished;
use Illuminate\Log\Logger;

class OnScreenshotStored extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(ScreenshotStored $screenshotStored): void
    {
        $this->logger->channel('audit')->info('スクリーンショット作成', $screenshotStored->screenshot->getInfoLogging());

        $screenshotStored->screenshot->notify(new SendScreenshotPublished());
    }
}
