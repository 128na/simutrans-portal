<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\Redirect\DeleteRedirect;
use App\Actions\Redirect\FindMyRedirects;
use App\Http\Controllers\Controller;
use App\Models\Redirect;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RedirectController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(FindMyRedirects $findMyRedirects): View
    {
        $user = $this->loggedinUser();

        return view('mypage.redirects', [
            'redirects' => $findMyRedirects($user),
            'meta' => $this->metaOgpService->mypageRedirects(),
        ]);
    }

    public function destroy(Redirect $redirect, DeleteRedirect $deleteRedirect): RedirectResponse
    {
        $this->authorize('update', $redirect);

        $deleteRedirect($redirect);

        return to_route('mypage.redirects')->with('status', '削除しました');
    }
}
