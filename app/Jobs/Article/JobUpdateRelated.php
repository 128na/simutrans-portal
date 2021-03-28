<?php

namespace App\Jobs\Article;

use App\Models\PakAddonCount;
use App\Models\UserAddonCount;
use App\Repositories\TagRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class JobUpdateRelated implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        UserAddonCount::recount();
        PakAddonCount::recount();

        $this->tagRepository->deleteUnrelated();
        Cache::flush();
    }
}
