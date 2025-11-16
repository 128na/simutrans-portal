<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Actions\DiscordInvite\GenerateInviteCodeAction;
use App\Services\Front\MetaOgpService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Throwable;

final class DiscordController extends Controller
{
    public function __construct(
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        return view('v2.discord.index', [
            'meta' => $this->metaOgpService->discord(),
        ]);
    }

    public function generate(Request $request, GenerateInviteCodeAction $generateInviteCodeAction): \Illuminate\Contracts\View\View
    {

        try {
            $recaptchaToken = $request->input('recaptchaToken', '');
            $url = $generateInviteCodeAction($recaptchaToken);

            return view('v2.discord.index', [
                'url' => $url,
                'meta' => $this->metaOgpService->discord(),
            ]);
        } catch (Throwable) {
            abort(400);
        }
    }
}
