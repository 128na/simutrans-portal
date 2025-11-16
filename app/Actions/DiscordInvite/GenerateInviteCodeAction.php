<?php

declare(strict_types=1);

namespace App\Actions\DiscordInvite;

use App\Services\Discord\InviteService;
use App\Services\Google\Recaptcha\RecaptchaService;

final readonly class GenerateInviteCodeAction
{
    public function __construct(
        private InviteService $inviteService,
        private RecaptchaService $recaptchaService,
    ) {}

    public function __invoke(string $recaptchaToken): string
    {
        $this->recaptchaService->assessment($recaptchaToken);
        $url = $this->inviteService->create();

        event(new \App\Events\Discord\DiscordInviteCodeCreated);

        return $url;
    }
}
