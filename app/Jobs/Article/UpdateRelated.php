<?php

namespace App\Jobs\Article;

use App\Models\PakAddonCount;
use App\Models\Tag;
use App\Models\UserAddonCount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class UpdateRelated implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        UserAddonCount::recount();
        PakAddonCount::recount();
        Tag::deleteUnrelated();
        Cache::flush();
    }
}
