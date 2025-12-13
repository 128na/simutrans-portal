<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Repositories\LoginHistoryRepository;
use App\Repositories\UserRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly LoginHistoryRepository $loginHistoryRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        return view('mypage.index', [
            'user' => $user,
            'summary' => $this->userRepository->getSummary($user),
            'meta' => $this->metaOgpService->mypage(),
        ]);
    }

    public function verifyEmail(): View
    {
        return view('mypage.verify-email', [
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
        $user = Auth::user();

        return view('mypage.two-factor', [
            'user' => $user,
            'meta' => $this->metaOgpService->mypageTwoFactor(),
        ]);
    }

    public function loginHistories(): View
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        return view('mypage.login-histories', [
            'loginHistories' => $this->loginHistoryRepository->getByUser($user->id),
            'meta' => $this->metaOgpService->mypageLoginHistories(),
        ]);
    }
}
