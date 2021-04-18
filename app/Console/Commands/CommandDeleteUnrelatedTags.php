<?php

namespace App\Console\Commands;

use App\Jobs\Article\JobDeleteUnrelatedTags;
use Illuminate\Console\Command;

class CommandDeleteUnrelatedTags extends Command
{
    protected $signature = 'delete:tags';

    protected $description = '記事に紐づいていないタグを削除する';

    public function handle()
    {
        JobDeleteUnrelatedTags::dispatch();

        return 0;
    }
}
