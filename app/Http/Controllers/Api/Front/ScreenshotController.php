<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Front;

use App\Actions\Screenshot\ListPublicScreenshot;
use App\Actions\Screenshot\ShowPublicScreenshot;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Front\Screenshot as ScreenshotResource;
use App\Models\Screenshot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ScreenshotController extends Controller
{
    public function index(Request $request, ListPublicScreenshot $listPublicScreenshot): AnonymousResourceCollection
    {
        return $listPublicScreenshot->list();
    }

    public function show(Screenshot $screenshot, ShowPublicScreenshot $showPublicScreenshot): ScreenshotResource
    {
        $this->authorize('showPublic', $screenshot);

        return $showPublicScreenshot->show($screenshot);
    }
}
