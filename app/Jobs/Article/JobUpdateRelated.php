<?php

namespace App\Jobs\Article;

use App\Repositories\PakAddonCountRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserAddonCountRepository;
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
    private PakAddonCountRepository $pakAddonCountRepository;
    private UserAddonCountRepository $userAddonCountRepository;

    public function __construct(
        TagRepository $tagRepository,
        PakAddonCountRepository $pakAddonCountRepository,
        UserAddonCountRepository $userAddonCountRepository
    ) {
        $this->tagRepository = $tagRepository;
        $this->pakAddonCountRepository = $pakAddonCountRepository;
        $this->userAddonCountRepository = $userAddonCountRepository;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->pakAddonCountRepository->recount();
        $this->userAddonCountRepository->recount();
        $this->tagRepository->deleteUnrelated();

        Cache::flush();
    }
}
