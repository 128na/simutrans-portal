<?php

declare(strict_types=1);

namespace App\Events\Screenshot;

use App\Models\Screenshot;
use Illuminate\Queue\SerializesModels;

class ScreenshotStored
{
    use SerializesModels;

    public function __construct(public readonly Screenshot $screenshot)
    {
    }
}