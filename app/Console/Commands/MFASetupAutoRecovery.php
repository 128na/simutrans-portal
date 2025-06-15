<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\MFA\RecoveryIncompleteUsers;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class MFASetupAutoRecovery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mfa-setup-auto-recovery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '未完了の二要素認証設定を自動回復し、ユーザーへ通知します。';

    /**
     * Execute the console command.
     */
    public function handle(RecoveryIncompleteUsers $recoveryIncompleteUsers): int
    {
        try {
            $recoveryIncompleteUsers();
        } catch (\Throwable $throwable) {
            $this->error('MFA Setup Auto Recovery failed: '.$throwable->getMessage());
            Log::error('MFA Setup Auto Recovery failed', [
                'error' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
