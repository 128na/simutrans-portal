<?php

declare(strict_types=1);

namespace App\Actions\Screenshot;

use App\Http\Resources\Api\Front\Screenshot as ScreenshotResource;
use App\Models\Screenshot;
use App\Repositories\ScreenshotRepository;

class ShowPublicScreenshot
{
    public function __construct(
        private readonly ScreenshotRepository $screenshotRepository,
    ) {
    }

    public function show(Screenshot $screenshot): ScreenshotResource
    {
        $screenshot->loadMissing(['user', 'attachments.fileInfo', 'articles']);

        return ScreenshotResource::make($screenshot);
    }
}
