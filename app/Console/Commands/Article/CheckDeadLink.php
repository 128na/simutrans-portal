<?php

declare(strict_types=1);

namespace App\Console\Commands\Article;

use App\Actions\ArticleClosure\Recover;
use App\Actions\ArticleClosure\Runs;
use App\Actions\DeadLink\Check;
use App\Actions\DeadLink\ProcessDeadLink;
use App\Models\Article;
use Illuminate\Console\Command;
use Throwable;

class CheckDeadLink extends Command
{
    protected $signature = 'check:deadlink';

    protected $description = '公開済みのアドオン紹介記事でリンク切れのものを確認する。リンク切れのものはステータスを非公開にする';

    public function handle(Check $check, ProcessDeadLink $process, Runs $runs, Recover $recover): int
    {
        $run = $runs->start();
        $interrupted = true;
        try {
            $check(
                fn (Article $article): bool => $process($article, $run->id),
                fn (): bool => $runs->heartbeat($run->id),
                fn (Article $article) => $process->alive($article, $run->id),
            );
            $interrupted = false;
        } catch (Throwable $throwable) {
            report($throwable);

            return Command::FAILURE;
        } finally {
            $runs->finalize($run->id, $interrupted);
            $recover();
        }

        return Command::SUCCESS;
    }
}
