<?php

declare(strict_types=1);

namespace App\Listeners\Article;

use App\Events\Article\ScreenshotUpdated;
use App\Listeners\BaseListener;
use Illuminate\Log\Logger;

class OnScreenshotUpdated extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(ScreenshotUpdated $screenshotUpdated): void
    {
        $this->logger->channel('audit')->info('スクショ作成', $screenshotUpdated->screenshot->getInfoLogging());
    }
}
