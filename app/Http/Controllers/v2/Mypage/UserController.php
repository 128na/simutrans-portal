<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2\Mypage;

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
        return view('v2.user.invite', [
            'invitee' => $user,
            'meta' => $this->metaOgpService->registration(),
        ]);
    }

    public function registration(User $user, StoreRequest $storeRequest, Registration $registration): \Illuminate\Contracts\View\View
    {
        $data = $storeRequest->validated();
        $inviter = $registration($data, $user);

        return view('v2.user.welcome', [
            'inviter' => $inviter,
            'meta' => $this->metaOgpService->login(),
        ]);
    }

    public function showLogin(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        if (Auth::check()) {
            return to_route('mypage.index');
        }

        return view('v2.user.login', [
            'meta' => $this->metaOgpService->login(),
        ]);
    }

    public function showTwoFactor(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        return view('v2.user.two-factor', [
            'meta' => $this->metaOgpService->login(),
        ]);
    }

    public function showForgotPassword(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        return view('v2.user.forget-password', [
            'meta' => $this->metaOgpService->login(),
        ]);
    }

    public function showResetPassword(string $token): \Illuminate\Contracts\View\View|RedirectResponse
    {
        return view('v2.user.reset-password', [
            'token' => $token,
            'meta' => $this->metaOgpService->resetPassword(),
        ]);
    }
}
