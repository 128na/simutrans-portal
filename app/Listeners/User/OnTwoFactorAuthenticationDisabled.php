<?php

declare(strict_types=1);

namespace App\Listeners\User;

use Illuminate\Log\Logger;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;

final readonly class OnTwoFactorAuthenticationDisabled
{
    public function __construct(private Logger $logger)
    {
    }

    public function handle(TwoFactorAuthenticationDisabled $twoFactorAuthenticationDisabled): void
    {
        $this->logger->channel('audit')->info('2要素認証無効化', $twoFactorAuthenticationDisabled->user->getInfoLogging());
    }
}
