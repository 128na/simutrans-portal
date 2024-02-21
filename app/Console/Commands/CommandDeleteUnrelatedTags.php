<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\Article\JobDeleteUnrelatedTags;
use Illuminate\Console\Command;
use Throwable;

class CommandDeleteUnrelatedTags extends Command
{
    protected $signature = 'delete:tags';

    protected $description = '記事に紐づいていないタグを削除する';

    public function handle(): int
    {
        try {
            JobDeleteUnrelatedTags::dispatchSync();
        } catch (Throwable $throwable) {
            report($throwable);

            return 1;
        }

        return 0;
    }
}
