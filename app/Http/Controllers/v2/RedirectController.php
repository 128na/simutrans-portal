<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Models\Redirect;
use App\Services\Front\MetaOgpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class RedirectController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        return view('v2.mypage.redirects', [
            'redirects' => Auth::user()->redirects()->get(),
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }
    public function destroy(Redirect $redirect): RedirectResponse
    {
        if (Auth::user()->cannot('update', $redirect)) {
            return abort(403);
        }
        $redirect->delete();

        return to_route('mypage.redirects')->with('status', '削除しました');
    }
}
