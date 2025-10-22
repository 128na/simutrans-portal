<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\TwoFactorAuthenticationIncomplete;
use Illuminate\Log\Logger;

final readonly class OnTwoFactorAuthenticationIncomplete
{
    public function __construct(private Logger $logger) {}

    public function handle(TwoFactorAuthenticationIncomplete $twoFactorAuthenticationIncomplete): void
    {
        $this->logger->channel('audit')->info('2要素認証未完了', $twoFactorAuthenticationIncomplete->user->getInfoLogging());
    }
}
