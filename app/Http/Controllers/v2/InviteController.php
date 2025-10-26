<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Services\Front\MetaOgpService;
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
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }

    public function createOrUpdate(): \Illuminate\Contracts\View\View
    {
        Auth::user()->update(['invitation_code' => Str::uuid()]);
        event(new \App\Events\User\InviteCodeCreated(Auth::user()));

        return view('v2.mypage.invite', [
            'user' => Auth::user()->loadMissing('invites'),
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }

    public function revoke(): \Illuminate\Contracts\View\View
    {
        Auth::user()->update(['invitation_code' => null]);
        event(new \App\Events\User\InviteCodeCreated(Auth::user()));

        return view('v2.mypage.invite', [
            'user' => Auth::user()->loadMissing('invites'),
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }
}
