<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Services\Front\MetaOgpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class MypageController extends Controller
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

    public function verifyEmail(): \Illuminate\Contracts\View\View
    {
        return view('v2.mypage.verify-email', [
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }

    public function verifyNotice(): RedirectResponse
    {
        return to_route('mypage.verify-email')
            ->with('error', 'この機能を使うにはメールアドレスの認証を完了させる必要があります。');
    }

    public function twoFactor(): \Illuminate\Contracts\View\View
    {
        return view('v2.mypage.two-factor', [
            'user' => Auth::user(),
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }

    public function loginHistories(): \Illuminate\Contracts\View\View
    {
        return view('v2.mypage.login-histories', [
            'loginHistories' => Auth::user()->loginHistories()->orderBy('created_at', 'desc')->limit(10)->get(),
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }

    public function profile(): \Illuminate\Contracts\View\View
    {
        return view('v2.mypage.index', [
            'user' => Auth::user(),
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }
}
