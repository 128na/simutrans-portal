<?php

namespace App\Jobs\BulkZip;

use App\Models\BulkZip;
use App\Repositories\BulkZipRepository;
use App\Services\BulkZip\ZipManager;
use App\Services\BulkZip\ZippableManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobCreateBulkZip implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private BulkZip $bulkZip;

    public function __construct(BulkZip $bulkZip)
    {
        $this->bulkZip = $bulkZip;
    }

    public function handle(
        BulkZipRepository $bulkZipRepository,
        ZippableManager $zippableManager,
        ZipManager $zipManager
    ) {
        // dispatchAfterResponseではfailedメソッドは呼ばれない
        try {
            if ($this->bulkZip->generated) {
                return;
            }

            $begin = microtime(true);

            $items = $zippableManager->getItems($this->bulkZip);
            $path = $zipManager->create($items);
            $bulkZipRepository->update($this->bulkZip, ['generated' => true, 'path' => $path]);

            logger(sprintf('JobCreateBulkZip::handle %.2f sec.', microtime(true) - $begin));
        } catch (\Throwable $e) {
            logger('JobCreateBulkZip failed');
            $this->bulkZip->delete();
            report($e);
        }
    }
}
