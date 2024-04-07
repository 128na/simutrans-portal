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
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Screenshot;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ScreenshotController extends Controller
{
    public function index(ListScreenshot $listScreenshot): AnonymousResourceCollection
    {
        return $listScreenshot->list($this->loggedinUser());
    }

    public function store(StoreRequest $storeRequest, StoreScreenshot $storeScreenshot): AnonymousResourceCollection
    {
        /**
         * @var array{should_notify:bool,screenshot:array{title:string,description:string,links:string[],status:string,attachments:array<int,array{id:int,caption:string,order:int}>,articles:array<int,array{id:int,title:string}>}}
         */
        $validated = $storeRequest->validated();
        $storeScreenshot->store($this->loggedinUser(), $validated);
        JobUpdateRelated::dispatch();

        return $this->index(app(ListScreenshot::class));
    }

    public function update(Screenshot $screenshot, UpdateRequest $updateRequest, UpdateScreenshot $updateScreenshot): AnonymousResourceCollection
    {
        $this->authorize('update', $screenshot);
        /**
         * @var array{should_notify:bool,screenshot:array{id:int,title:string,description:string,links:string[],status:string,attachments:array<int,array{id:int,caption:string,order:int}>,articles:array<int,array{id:int,title:string}>}}
         */
        $validated = $updateRequest->validated();
        $updateScreenshot->update($screenshot, $validated);
        JobUpdateRelated::dispatch();

        return $this->index(app(ListScreenshot::class));
    }

    public function destroy(Screenshot $screenshot, DestroyScreenshot $destroyScreenshot): AnonymousResourceCollection
    {
        $this->authorize('destroy', $screenshot);
        $destroyScreenshot->destroy($screenshot);
        JobUpdateRelated::dispatch();

        return $this->index(app(ListScreenshot::class));
    }
}
