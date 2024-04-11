<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\DeadLink\Check;
use App\Actions\DeadLink\OnDead;
use Illuminate\Console\Command;
use Throwable;

final class DeadLinkChecker extends Command
{
    protected $signature = 'check:deadlink';

    protected $description = '公開済みのアドオン紹介記事でリンク切れのものを確認する。リンク切れのものはステータスを非公開にする';

    public function handle(Check $check, OnDead $onDead): int
    {
        try {
            $check($onDead);
        } catch (Throwable $throwable) {
            report($throwable);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
