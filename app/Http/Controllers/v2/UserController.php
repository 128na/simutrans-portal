<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Actions\User\Registration;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class UserController extends Controller
{
    public function showInvite(User $user): \Illuminate\Contracts\View\View
    {
        return view('v2.user.invite', ['invitee' => $user]);
    }

    public function registration(User $user, StoreRequest $storeRequest, Registration $registration): \Illuminate\Contracts\View\View
    {
        $data = $storeRequest->validated();
        $inviter = $registration($data, $user);

        return view('v2.user.welcome', ['inviter' => $inviter]);
    }

    public function showLogin(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        if (Auth::check()) {
            return to_route('mypage.index');
        }

        return view('v2.user.login');
    }

    public function showTwoFactor(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        return view('v2.user.two-factor');
    }

    public function showForgotPassword(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        return view('v2.user.forget-password');
    }

    public function showResetPassword(string $token): \Illuminate\Contracts\View\View|RedirectResponse
    {
        return view('v2.user.reset-password', ['token' => $token]);
    }
}
