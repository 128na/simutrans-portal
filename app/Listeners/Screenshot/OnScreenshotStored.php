<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\Article\ScreenshotStored;
use App\Listeners\BaseListener;
use Illuminate\Log\Logger;

class OnScreenshotStored extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(ScreenshotStored $screenshotStored): void
    {
        $this->logger->channel('audit')->info('スクショ作成', $screenshotStored->screenshot->getInfoLogging());
    }
}
