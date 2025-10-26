<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Services\Front\MetaOgpService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class AnalyticsController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(Request $request): \Illuminate\Contracts\View\View
    {
        // TODO: analytics logic
        return view('v2.mypage.index', [
            'user' => Auth::user(),
            'meta' => $this->metaOgpService->analytics(),
        ]);
    }
}
