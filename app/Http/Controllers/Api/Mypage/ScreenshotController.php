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
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ScreenshotController extends Controller
{
    public function index(ListScreenshot $listScreenshot): AnonymousResourceCollection
    {
        return $listScreenshot->list($this->loggedinUser());
    }

    public function store(StoreRequest $storeRequest, StoreScreenshot $storeScreenshot): AnonymousResourceCollection
    {
        $storeScreenshot->store($this->loggedinUser(), $storeRequest->validated());

        return $this->index(app(ListScreenshot::class));
    }

    public function update(Screenshot $screenshot, UpdateRequest $updateRequest, UpdateScreenshot $updateScreenshot): AnonymousResourceCollection
    {
        $this->authorize('update', $screenshot);
        $updateScreenshot->update($screenshot, $updateRequest->validated());

        return $this->index(app(ListScreenshot::class));
    }

    public function destroy(Screenshot $screenshot, DestroyScreenshot $destroyScreenshot): AnonymousResourceCollection
    {
        $this->authorize('destroy', $screenshot);
        $destroyScreenshot->destroy($screenshot);

        return $this->index(app(ListScreenshot::class));
    }
}
