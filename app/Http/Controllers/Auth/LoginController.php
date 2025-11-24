<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class LoginController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function showLogin(): RedirectResponse|View
    {
        if (Auth::check()) {
            return to_route('mypage.index');
        }

        return view('auth.login', [
            'meta' => $this->metaOgpService->mypageLogin(),
        ]);
    }
}
