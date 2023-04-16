<?php

declare(strict_types=1);

namespace App\Jobs\Article;

use App\Jobs\StaticJson\GenerateSidebar;
use App\Jobs\StaticJson\GenerateTopOrderByModifiedAt;
use App\Jobs\StaticJson\GenerateTopOrderByPublishedAt;
use App\Repositories\PakAddonCountRepository;
use App\Repositories\UserAddonCountRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

/**
 * サイドバー表示用のデータを更新する.
 */
class JobUpdateRelated implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(
        PakAddonCountRepository $pakAddonCountRepository,
        UserAddonCountRepository $userAddonCountRepository
    ): void {
        $pakAddonCountRepository->recount();
        $userAddonCountRepository->recount();

        Cache::flush();

        GenerateSidebar::dispatchSync();
        GenerateTopOrderByModifiedAt::dispatchSync();
        GenerateTopOrderByPublishedAt::dispatchSync();
    }
}
