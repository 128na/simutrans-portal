<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Actions\DiscordInvite\GenerateInviteCodeAction;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Throwable;

class DiscordController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        return view('pages.discord.index', [
            'meta' => $this->metaOgpService->frontDiscord(),
        ]);
    }

    public function generate(Request $request, GenerateInviteCodeAction $generateInviteCodeAction): View
    {

        try {
            $recaptchaToken = $request->string('recaptchaToken', '')->value();
            $url = $generateInviteCodeAction($recaptchaToken);

            return view('pages.discord.index', [
                'url' => $url,
                'meta' => $this->metaOgpService->frontDiscord(),
            ]);
        } catch (Throwable) {
            abort(400);
        }
    }
}
