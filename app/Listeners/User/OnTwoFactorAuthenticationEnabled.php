<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Listeners\BaseListener;
use Illuminate\Log\Logger;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;

class OnTwoFactorAuthenticationEnabled extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(TwoFactorAuthenticationEnabled $twoFactorAuthenticationEnabled): void
    {
        $this->logger->channel('audit')->info('2要素認証有効化', $this->getUserInfo($twoFactorAuthenticationEnabled->user));
    }
}
