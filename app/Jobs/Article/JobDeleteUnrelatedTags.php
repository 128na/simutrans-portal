<?php

namespace App\Jobs\Article;

use App\Repositories\TagRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

/**
 * 記事に紐づいていないタグを削除する.
 */
class JobDeleteUnrelatedTags implements ShouldQueue
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
    public function handle(TagRepository $tagRepository)
    {
        $tagRepository->deleteUnrelated();
        Cache::flush();
    }
}
