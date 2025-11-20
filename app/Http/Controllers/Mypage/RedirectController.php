<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\Redirect\DeleteRedirect;
use App\Actions\Redirect\FindMyRedirects;
use App\Models\Redirect;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class RedirectController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(FindMyRedirects $findMyRedirects): View
    {
        $user = Auth::user();

        return view('v2.mypage.redirects', [
            'redirects' => $findMyRedirects($user),
            'meta' => $this->metaOgpService->mypageRedirects(),
        ]);
    }

    public function destroy(Redirect $redirect, DeleteRedirect $deleteRedirect): RedirectResponse
    {
        $user = Auth::user();
        if ($user->cannot('update', $redirect)) {
            return abort(403);
        }

        $deleteRedirect($redirect);

        return to_route('mypage.redirects')->with('status', '削除しました');
    }
}
