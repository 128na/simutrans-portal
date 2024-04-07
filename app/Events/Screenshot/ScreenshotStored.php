<?php

declare(strict_types=1);

namespace App\Events\Screenshot;

use App\Models\Screenshot;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScreenshotStored
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly Screenshot $screenshot,
        public readonly bool $shouldNotify = false,
    ) {
    }
}
