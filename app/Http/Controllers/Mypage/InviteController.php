<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Events\User\InviteCodeCreated;
use App\Http\Controllers\Controller;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class InviteController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = $this->loggedinUser();

        return view('mypage.invite', [
            'user' => $user->loadMissing('invites'),
            'meta' => $this->metaOgpService->mypageInvite(),
        ]);
    }

    public function createOrUpdate(): RedirectResponse
    {
        $user = $this->loggedinUser();

        $user->update(['invitation_code' => Str::uuid()]);
        event(new InviteCodeCreated($user));

        return to_route('mypage.invite')->with('status', '招待コードを発行しました');
    }

    public function revoke(): RedirectResponse
    {
        $user = $this->loggedinUser();

        $user->update(['invitation_code' => null]);
        event(new InviteCodeCreated($user));

        return to_route('mypage.invite')->with('status', '招待コードを削除しました');
    }
}
