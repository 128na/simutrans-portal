<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\UserWithdrawn;
use Illuminate\Log\Logger;

class OnUserWithdrawn
{
    public function __construct(private Logger $logger) {}

    public function handle(UserWithdrawn $userWithdrawn): void
    {
        $this->logger->channel('audit')->info('ユーザー退会', $userWithdrawn->user->getInfoLogging());
    }
}
