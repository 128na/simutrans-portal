<?php

declare(strict_types=1);

namespace App\Listeners\User;

use Illuminate\Log\Logger;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;

final readonly class OnTwoFactorAuthenticationEnabled
{
    public function __construct(
        private Logger $logger,
    ) {}

    public function handle(TwoFactorAuthenticationEnabled $twoFactorAuthenticationEnabled): void
    {
        $this->logger->channel('audit')->info(
            '二要素認証有効化',
            $twoFactorAuthenticationEnabled->user->getInfoLogging(),
        );
    }
}
