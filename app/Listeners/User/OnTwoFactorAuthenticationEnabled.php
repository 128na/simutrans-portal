<?php

declare(strict_types=1);

namespace App\Listeners\User;

use Illuminate\Log\Logger;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;

final readonly class OnTwoFactorAuthenticationEnabled
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(TwoFactorAuthenticationEnabled $twoFactorAuthenticationEnabled): void
    {
        $this->logger->channel('audit')->info('2要素認証有効化', $twoFactorAuthenticationEnabled->user->getInfoLogging());
    }
}
