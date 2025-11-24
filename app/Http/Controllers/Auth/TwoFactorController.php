<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class TwoFactorController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function showTwoFactor(): RedirectResponse|View
    {
        return view('auth.two-factor', [
            'meta' => $this->metaOgpService->mypageLogin(),
        ]);
    }
}
