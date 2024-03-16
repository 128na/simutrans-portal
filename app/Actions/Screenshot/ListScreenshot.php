<?php

declare(strict_types=1);

namespace App\Actions\Screenshot;

use App\Http\Resources\Api\Mypage\Screenshot as ScreenshotResource;
use App\Models\User;
use App\Repositories\ScreenshotRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListScreenshot
{
    public function __construct(
        private readonly ScreenshotRepository $screenshotRepository,
    ) {
    }

    public function list(User $user): AnonymousResourceCollection
    {
        return ScreenshotResource::collection($this->screenshotRepository->findAllByUser($user));
    }
}
