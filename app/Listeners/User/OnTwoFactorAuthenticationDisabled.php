<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Listeners\BaseListener;
use Illuminate\Log\Logger;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;

class OnTwoFactorAuthenticationDisabled extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(TwoFactorAuthenticationDisabled $event): void
    {
        $this->logger->channel('audit')->info('2要素認証無効化', $this->getUserInfo($event->user));
    }
}
