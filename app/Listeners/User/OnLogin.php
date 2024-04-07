<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Models\User;
use App\Notifications\SendLoggedInEmail;
use Illuminate\Auth\Events\Login;
use Illuminate\Log\Logger;

final readonly class OnLogin
{
    public function __construct(private Logger $logger)
    {
    }

    public function handle(Login $login): void
    {
        if ($login->user instanceof User && $this->isNewLogin()) {
            $this->logger->channel('audit')->info('ログイン', $login->user->getInfoLogging());

            $login->user->notify(new SendLoggedInEmail($login->user->loginHistories()->create()));
        }
    }

    private function isNewLogin(): bool
    {
        $trace = json_encode(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20)) ?: '';

        return match (true) {
            str_contains($trace, 'AuthenticatedSessionController') => true,
            str_contains($trace, 'TwoFactorAuthenticatedSessionController') => true,
            default => false,
        };
    }
}
