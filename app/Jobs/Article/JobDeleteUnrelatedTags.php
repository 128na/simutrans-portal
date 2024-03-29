<?php

declare(strict_types=1);

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

    public function handle(TagRepository $tagRepository): void
    {
        $tagRepository->deleteUnrelated();
        Cache::flush();
    }
}
