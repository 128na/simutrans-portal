<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;

class SupportController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function support(): View
    {
        return view('pages.support.index', [
            'meta' => $this->metaOgpService->frontSupport(),
        ]);
    }
}
