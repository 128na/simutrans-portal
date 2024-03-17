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
    public function index(Request $request, ListScreenshot $listScreenshot): AnonymousResourceCollection
    {
        return $listScreenshot->list($request->user());
    }

    public function store(StoreRequest $storeRequest, StoreScreenshot $storeScreenshot): AnonymousResourceCollection
    {
        $storeScreenshot->store($storeRequest->user(), $storeRequest->validated());

        return $this->index($storeRequest, app(ListScreenshot::class));
    }

    public function update(Screenshot $screenshot, UpdateRequest $updateRequest, UpdateScreenshot $updateScreenshot): AnonymousResourceCollection
    {
        $this->authorize('update', $screenshot);
        $updateScreenshot->update($screenshot, $updateRequest->validated());

        return $this->index($updateRequest, app(ListScreenshot::class));
    }

    public function destroy(Screenshot $screenshot, Request $request, DestroyScreenshot $destroyScreenshot): AnonymousResourceCollection
    {
        $this->authorize('destroy', $screenshot);
        $destroyScreenshot->destroy($screenshot);

        return $this->index($request, app(ListScreenshot::class));
    }
}
