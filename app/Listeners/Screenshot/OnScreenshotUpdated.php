<?php

declare(strict_types=1);

namespace App\Listeners\Screenshot;

use App\Enums\ScreenshotStatus;
use App\Events\Screenshot\ScreenshotUpdated;
use App\Notifications\SendScreenshotPublished;
use Illuminate\Log\Logger;

final readonly class OnScreenshotUpdated
{
    public function __construct(private Logger $logger)
    {
    }

    public function handle(ScreenshotUpdated $screenshotUpdated): void
    {
        $this->logger->channel('audit')->info('スクリーンショット更新', $screenshotUpdated->screenshot->getInfoLogging());

        // 公開以外
        if ($screenshotUpdated->screenshot->status !== ScreenshotStatus::Publish) {
            return;
        }

        // 通知を希望しない
        if (! $screenshotUpdated->shouldNotify) {
            return;
        }

        // published_atがnullから初めて変わった場合は新規投稿扱い
        if ($screenshotUpdated->notYetPublished) {
            $screenshotUpdated->screenshot->notify(new SendScreenshotPublished());
        }
    }
}
