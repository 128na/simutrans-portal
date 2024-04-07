<?php

declare(strict_types=1);

namespace App\Actions\Screenshot;

use App\Http\Resources\Api\Front\Screenshot as ScreenshotResource;
use App\Models\Screenshot;

final class ShowPublicScreenshot
{
    public function show(Screenshot $screenshot): ScreenshotResource
    {
        $screenshot->loadMissing(['user', 'attachments.fileInfo', 'articles']);

        return ScreenshotResource::make($screenshot);
    }
}
