<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Services\Front\MetaOgpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class InviteController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        return view('v2.mypage.invite', [
            'user' => Auth::user()->loadMissing('invites'),
            'meta' => $this->metaOgpService->invite(),
        ]);
    }

    public function createOrUpdate(): RedirectResponse
    {
        Auth::user()->update(['invitation_code' => Str::uuid()]);
        event(new \App\Events\User\InviteCodeCreated(Auth::user()));

        return to_route('mypage.invite')->with('status', '招待コードを発行しました');
    }

    public function revoke(): RedirectResponse
    {
        Auth::user()->update(['invitation_code' => null]);
        event(new \App\Events\User\InviteCodeCreated(Auth::user()));

        return to_route('mypage.invite')->with('status', '招待コードを削除しました');
    }
}
