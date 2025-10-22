<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Log\Logger;

final readonly class OnRegistered
{
    public function __construct(private Logger $logger) {}

    public function handle(Registered $registered): void
    {
        if ($registered->user instanceof User) {
            $this->logger->channel('audit')->info('ユーザー登録', $registered->user->getInfoLogging());
        }
    }
}
