<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Services\Discord\InviteService;
use App\Services\Google\Recaptcha\RecaptchaException;
use App\Services\Google\Recaptcha\RecaptchaService;
use App\Services\Logging\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DiscordController extends Controller
{
    public function __construct(
        private InviteService $inviteService,
        private RecaptchaService $recaptchaService,
        private AuditLogService $auditLogService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $this->recaptchaService->assessment($request->string('token', '')->toString());
            $url = $this->inviteService->create();
            $this->auditLogService->discordInviteCodeCreate($request);

            return response()->json(['url' => $url], 200);
        } catch (RecaptchaException $e) {
            $this->auditLogService->discordInviteCodeReject($request);
            report($e);

            return response()->json(['url' => null], 400);
        } catch (Throwable $e) {
            $this->auditLogService->discordInviteCodeReject($request);
            report($e);

            return response()->json(['url' => null], 400);
        }
    }
}
