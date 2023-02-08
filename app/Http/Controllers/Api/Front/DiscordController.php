<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Services\Discord\InviteService;
use App\Services\Google\Recaptcha\RecaptchaException;
use App\Services\Google\Recaptcha\RecaptchaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DiscordController extends Controller
{
    public function __construct(
        private InviteService $inviteService,
        private RecaptchaService $recaptchaService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $this->logging();
            $this->recaptchaService->assessment($request->string('token', '')->toString());

            return response()->json(['url' => $this->inviteService->create()], 200);
        } catch (RecaptchaException $e) {
            report($e);

            return response()->json(['url' => null], 400);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['url' => null], 400);
        }
    }

    private function logging(): void
    {
        logger()->channel('discord-invite')->info('invite', [
            request()->server('SERVER_ADDR', 'N/A'),
            request()->server('HTTP_REFERER', 'N/A'),
            request()->server('HTTP_USER_AGENT', 'N/A'),
        ]);
    }
}
