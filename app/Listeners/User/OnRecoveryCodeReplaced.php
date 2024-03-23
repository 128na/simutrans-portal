<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Listeners\BaseListener;
use App\Models\User;
use Illuminate\Log\Logger;
use Laravel\Fortify\Events\RecoveryCodeReplaced;

class OnRecoveryCodeReplaced extends BaseListener
{
    public function __construct(private readonly Logger $logger)
    {
    }

    public function handle(RecoveryCodeReplaced $recoveryCodeReplaced): void
    {
        if ($recoveryCodeReplaced->user instanceof User) {
            $this->logger->channel('audit')->info('リカバリコード使用', $recoveryCodeReplaced->user->getInfoLogging());
        }
    }
}
