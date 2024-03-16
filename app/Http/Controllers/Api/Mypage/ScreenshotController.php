<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Actions\Screenshot\DestroyScreenshot;
use App\Actions\Screenshot\ListScreenshot;
use App\Actions\Screenshot\StoreScreenshot;
use App\Actions\Screenshot\UpdateScreenshot;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Screenshot\StoreRequest;
use App\Http\Requests\Api\Screenshot\UpdateRequest;
use App\Models\Screenshot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ScreenshotController extends Controller
{
    public function __construct(
        private readonly ListScreenshot $listScreenshot,
        private readonly StoreScreenshot $storeScreenshot,
        private readonly UpdateScreenshot $updateScreenshot,
        private readonly DestroyScreenshot $destroyScreenshot,
    ) {
    }

    public function list(Request $request): AnonymousResourceCollection
    {
        return $this->listScreenshot->list($request->user);
    }

    public function store(StoreRequest $storeRequest): AnonymousResourceCollection
    {
        $this->storeScreenshot->store($storeRequest->user, $storeRequest->validated());

        return $this->list($storeRequest);
    }

    public function update(Screenshot $screenshot, UpdateRequest $updateRequest): AnonymousResourceCollection
    {
        $this->authorize('update', $screenshot);
        $this->updateScreenshot->update($screenshot, $updateRequest->validated());

        return $this->list($updateRequest);
    }

    public function destroy(Screenshot $screenshot, Request $request): AnonymousResourceCollection
    {
        $this->authorize('destroy', $screenshot);
        $this->destroyScreenshot->destroy($screenshot);

        return $this->list($request);
    }
}
