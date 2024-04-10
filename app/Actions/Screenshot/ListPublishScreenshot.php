<?php

declare(strict_types=1);

namespace App\Actions\Screenshot;

use App\Http\Resources\Api\Front\Screenshot as ScreenshotResource;
use App\Repositories\ScreenshotRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final readonly class ListPublishScreenshot
{
    public function __construct(
        private ScreenshotRepository $screenshotRepository,
    ) {
    }

    public function list(): AnonymousResourceCollection
    {
        $screenshots = $this->screenshotRepository->paginatePublish();

        return ScreenshotResource::collection($screenshots);
    }
}
