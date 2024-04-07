<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Front;

use App\Events\Discord\DiscordInviteCodeCreated;
use App\Http\Controllers\Controller;
use App\Services\Discord\InviteService;
use App\Services\Google\Recaptcha\RecaptchaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

final class DiscordController extends Controller
{
    public function __construct(
        private readonly InviteService $inviteService,
        private readonly RecaptchaService $recaptchaService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $this->recaptchaService->assessment($request->string('token', '')->toString());
            $url = $this->inviteService->create();

            DiscordInviteCodeCreated::dispatch();

            return response()->json(['url' => $url], 200);
        } catch (Throwable $throwable) {
            report($throwable);

            return response()->json(['url' => null], 400);
        }
    }
}
