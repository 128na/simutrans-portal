<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\GenerateStatic\DeleteUnrelatedTags;
use Illuminate\Console\Command;

class RemoveUnusedTagsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-unused-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(DeleteUnrelatedTags $deleteUnrelatedTags): int
    {
        try {
            $deleteUnrelatedTags();
        } catch (\Throwable $throwable) {
            report($throwable);
            $this->error($throwable->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
