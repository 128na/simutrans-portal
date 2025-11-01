<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2\Front;

use App\Services\Discord\InviteService;
use App\Services\Front\MetaOgpService;
use App\Services\Google\Recaptcha\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Throwable;

final class DiscordController extends Controller
{
    public function __construct(
        private readonly InviteService $inviteService,
        private readonly RecaptchaService $recaptchaService,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        return view('v2.discord.index', [
            'meta' => $this->metaOgpService->discord(),
        ]);
    }

    public function generate(Request $request): \Illuminate\Contracts\View\View
    {
        try {
            $recaptchaToken = $request->input('recaptchaToken', '');
            $this->recaptchaService->assessment((string) $recaptchaToken);
            $url = $this->inviteService->create();

            event(new \App\Events\Discord\DiscordInviteCodeCreated);

            return view('v2.discord.index', [
                'url' => $url,
                'meta' => $this->metaOgpService->discord(),
            ]);
        } catch (Throwable) {
            abort(400);
        }
    }
}
