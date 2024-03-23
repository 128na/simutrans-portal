<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Listeners\BaseListener;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Log\Logger;

class OnRegistered extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(Registered $registered): void
    {
        if ($registered->user instanceof User) {
            $this->logger->channel('audit')->info('ユーザー登録', $registered->user->getInfoLogging());
        }
    }
}
