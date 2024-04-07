<?php

declare(strict_types=1);

namespace App\Events\Screenshot;

use App\Models\Screenshot;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final readonly class ScreenshotUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Screenshot $screenshot,
        public bool $shouldNotify = false,
        public bool $notYetPublished = true,
    ) {
    }
}
