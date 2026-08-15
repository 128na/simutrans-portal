<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Repositories\LoginHistoryRepository;
use App\Repositories\UserRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly LoginHistoryRepository $loginHistoryRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = $this->loggedinUser();

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
        $user = $this->loggedinUser();

        return view('mypage.two-factor', [
            'user' => $user,
            'meta' => $this->metaOgpService->mypageTwoFactor(),
        ]);
    }

    public function loginHistories(): View
    {
        $user = $this->loggedinUser();

        return view('mypage.login-histories', [
            'loginHistories' => $this->loginHistoryRepository->getByUser($user->id),
            'meta' => $this->metaOgpService->mypageLoginHistories(),
        ]);
    }
}
