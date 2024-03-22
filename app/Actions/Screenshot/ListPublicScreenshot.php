<?php

declare(strict_types=1);

namespace App\Actions\Screenshot;

use App\Http\Resources\Api\Front\Screenshot as ScreenshotResource;
use App\Repositories\ScreenshotRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListPublicScreenshot
{
    public function __construct(
        private readonly ScreenshotRepository $screenshotRepository,
    ) {
    }

    public function list(): AnonymousResourceCollection
    {
        $screenshots = $this->screenshotRepository->paginatePublish();

        return ScreenshotResource::collection($screenshots);
    }
}
