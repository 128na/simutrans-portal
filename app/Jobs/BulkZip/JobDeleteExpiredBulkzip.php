<?php

declare(strict_types=1);

namespace App\Jobs\BulkZip;

use App\Repositories\BulkZipRepository;
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
        foreach ($bulkZipRepository->cursorExpired() as $bulkZip) {
            $bulkZip->delete();
        }
    }
}
