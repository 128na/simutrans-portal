<?php

declare(strict_types=1);

namespace App\Jobs\BulkZip;

use App\Repositories\BulkZipRepository;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobDeleteExpiredBulkzip implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(BulkZipRepository $bulkZipRepository): void
    {
        $expiredAt = CarbonImmutable::now()->subHours(24);

        foreach ($bulkZipRepository->cursorExpired($expiredAt) as $lazyCollection) {
            $lazyCollection->delete();
        }
    }
}
