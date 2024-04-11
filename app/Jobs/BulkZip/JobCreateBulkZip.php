<?php

declare(strict_types=1);

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
use Throwable;

final class JobCreateBulkZip implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly BulkZip $bulkZip)
    {
    }

    public function handle(
        BulkZipRepository $bulkZipRepository,
        ZippableManager $zippableManager,
        ZipManager $zipManager,
    ): void {
        if ($this->bulkZip->generated) {
            return;
        }

        $items = $zippableManager->getItems($this->bulkZip);
        $path = $zipManager->create($items);
        $bulkZipRepository->update($this->bulkZip, ['generated' => true, 'path' => $path]);
    }

    public function failed(?Throwable $throwable): void
    {
        logger()->error('[JobCreateBulkZip] failed', ['id' => $this->bulkZip->id]);
        $this->bulkZip->delete();
        if ($throwable instanceof \Throwable) {
            report($throwable);
        }
    }
}
