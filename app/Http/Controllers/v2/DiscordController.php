<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Events\Discord\DiscordInviteCodeCreated;
use App\Services\Discord\InviteService;
use App\Services\Google\Recaptcha\RecaptchaService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Throwable;

final class DiscordController extends Controller
{
    public function __construct(
        private readonly InviteService $inviteService,
        private readonly RecaptchaService $recaptchaService,
    ) {}
    public function index()
    {
        return view('v2.discord.index');
    }

    public function generate(Request $request)
    {
        try {
            $recaptchaToken = $request->input('recaptchaToken', '');
            $this->recaptchaService->assessment((string)$recaptchaToken);
            $url = $this->inviteService->create();

            DiscordInviteCodeCreated::dispatch();
            return view('v2.discord.index', ['url' => $url]);
        } catch (Throwable $throwable) {
            abort(400);
        }
    }
}
