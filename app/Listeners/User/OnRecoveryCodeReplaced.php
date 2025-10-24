<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Models\User;
use Illuminate\Log\Logger;
use Laravel\Fortify\Events\RecoveryCodeReplaced;

final readonly class OnRecoveryCodeReplaced
{
    public function __construct(private Logger $logger) {}

    public function handle(RecoveryCodeReplaced $recoveryCodeReplaced): void
    {
        if ($recoveryCodeReplaced->user instanceof User) {
            $this->logger->channel('audit')->info('リカバリコード使用', $recoveryCodeReplaced->user->getInfoLogging());
        }
    }
}
