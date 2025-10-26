<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Services\Front\MetaOgpService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class ArticleController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        return view('v2.mypage.index', [
            'user' => Auth::user(),
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }
    public function create(): \Illuminate\Contracts\View\View
    {
        return view('v2.mypage.index', [
            'user' => Auth::user(),
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }
    public function edit(): \Illuminate\Contracts\View\View
    {
        return view('v2.mypage.index', [
            'user' => Auth::user(),
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }
}
