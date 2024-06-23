<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Screenshot;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\Support\Renderable;

final class ScreenshotController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): Renderable
    {
        $meta = $this->metaOgpService->screenshotIndex();

        return view('front.spa', ['meta' => $meta]);
    }

    public function show(Screenshot $screenshot): Renderable
    {
        $this->authorize('showPublic', $screenshot);
        $meta = $this->metaOgpService->screenshot($screenshot);

        return view('front.spa', ['meta' => $meta]);
    }
}
