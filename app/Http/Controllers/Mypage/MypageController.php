<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Repositories\UserRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class MypageController extends Controller
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        return view('v2.mypage.index', [
            'user' => Auth::user(),
            'summary' => $this->userRepository->getSummary(Auth::user()),
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }

    public function verifyEmail(): View
    {
        return view('v2.mypage.verify-email', [
            'meta' => $this->metaOgpService->mypageVerifyEmail(),
        ]);
    }

    public function verifyNotice(): RedirectResponse
    {
        return to_route('mypage.verify-email')
            ->with('error', 'この機能を使うにはメールアドレスの認証を完了させる必要があります。');
    }

    public function twoFactor(): View
    {
        return view('v2.mypage.two-factor', [
            'user' => Auth::user(),
            'meta' => $this->metaOgpService->mypageTwoFactor(),
        ]);
    }

    public function loginHistories(): View
    {
        return view('v2.mypage.login-histories', [
            'loginHistories' => Auth::user()->loginHistories()->orderBy('created_at', 'desc')->limit(10)->get(),
            'meta' => $this->metaOgpService->mypageLoginHistories(),
        ]);
    }
}
