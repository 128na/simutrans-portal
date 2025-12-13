<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PasswordController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function showForgotPassword(): RedirectResponse|View
    {
        return view('auth.forget-password', [
            'meta' => $this->metaOgpService->mypageLogin(),
        ]);
    }

    public function showResetPassword(string $token): RedirectResponse|View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'meta' => $this->metaOgpService->mypageResetPassword(),
        ]);
    }
}
