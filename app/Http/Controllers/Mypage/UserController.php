<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\User\Registration;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Models\User;
use App\Services\Front\MetaOgpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class UserController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function showInvite(User $user): \Illuminate\Contracts\View\View
    {
        return view('auth.invite', [
            'invitee' => $user,
            'meta' => $this->metaOgpService->mypageRegistration(),
        ]);
    }

    public function registration(User $user, StoreRequest $storeRequest, Registration $registration): \Illuminate\Contracts\View\View
    {
        $data = $storeRequest->validated();
        $inviter = $registration($data, $user);

        return view('auth.welcome', [
            'inviter' => $inviter,
            'meta' => $this->metaOgpService->mypageLogin(),
        ]);
    }

    public function showLogin(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        if (Auth::check()) {
            return to_route('mypage.index');
        }

        return view('auth.login', [
            'meta' => $this->metaOgpService->mypageLogin(),
        ]);
    }

    public function showTwoFactor(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        return view('auth.two-factor', [
            'meta' => $this->metaOgpService->mypageLogin(),
        ]);
    }

    public function showForgotPassword(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        return view('auth.forget-password', [
            'meta' => $this->metaOgpService->mypageLogin(),
        ]);
    }

    public function showResetPassword(string $token): \Illuminate\Contracts\View\View|RedirectResponse
    {
        return view('auth.reset-password', [
            'token' => $token,
            'meta' => $this->metaOgpService->mypageResetPassword(),
        ]);
    }
}
