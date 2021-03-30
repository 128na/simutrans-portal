<?php

namespace App\Console\Commands;

use App\Jobs\Article\JobDeleteUnreatedTags;
use Illuminate\Console\Command;

class CommandDeleteUnreatedTags extends Command
{
    protected $signature = 'delete:tags';

    protected $description = '記事に紐づいていないタグを削除する';

    public function handle()
    {
        JobDeleteUnreatedTags::dispatchSync();

        return 0;
    }
}
