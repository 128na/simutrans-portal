<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Actions\DiscordInvite\GenerateInviteCodeAction;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Throwable;

final class DiscordController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        return view('v2.discord.index', [
            'meta' => $this->metaOgpService->frontDiscord(),
        ]);
    }

    public function generate(Request $request, GenerateInviteCodeAction $generateInviteCodeAction): View
    {

        try {
            $recaptchaToken = $request->input('recaptchaToken', '');
            $url = $generateInviteCodeAction($recaptchaToken);

            return view('v2.discord.index', [
                'url' => $url,
                'meta' => $this->metaOgpService->frontDiscord(),
            ]);
        } catch (Throwable) {
            abort(400);
        }
    }
}
