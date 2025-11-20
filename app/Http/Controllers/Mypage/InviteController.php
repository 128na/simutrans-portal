<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class InviteController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();

        return view('v2.mypage.invite', [
            'user' => $user->loadMissing('invites'),
            'meta' => $this->metaOgpService->mypageInvite(),
        ]);
    }

    public function createOrUpdate(): RedirectResponse
    {
        $user = Auth::user();
        $user->update(['invitation_code' => Str::uuid()]);
        event(new \App\Events\User\InviteCodeCreated($user));

        return to_route('mypage.invite')->with('status', '招待コードを発行しました');
    }

    public function revoke(): RedirectResponse
    {
        $user = Auth::user();
        $user->update(['invitation_code' => null]);
        event(new \App\Events\User\InviteCodeCreated($user));

        return to_route('mypage.invite')->with('status', '招待コードを削除しました');
    }
}
