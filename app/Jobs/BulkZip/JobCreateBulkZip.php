<?php

namespace App\Jobs\BulkZip;

use App\Models\Article;
use App\Models\BulkZip;
use App\Models\User;
use App\Models\User\Bookmark;
use App\Repositories\BulkZipRepository;
use App\Services\BulkZip\ZipManager;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

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
        ZipManager $zipManager
    ) {
        if ($this->bulkZip->generated) {
            return;
        }

        $begin = microtime(true);

        $items = $this->getItems($this->bulkZip);
        $path = $zipManager->create($items);
        $bulkZipRepository->update($this->bulkZip, ['generated' => true, 'path' => $path]);

        logger(sprintf('JobCreateBulkZip::handle %.2f sec.', microtime(true) - $begin));
    }

    private function getItems(BulkZip $bulkZip): array
    {
        switch ($bulkZip->bulk_zippable_type) {
            case User::class:
                return $bulkZip->bulkZippable
                    ->articles()
                    ->get()
                    ->load(['categories', 'tags', 'attachments', 'user'])
                    ->all();
            case Bookmark::class:
                return $bulkZip->bulkZippable
                    ->bookmarkItems()
                    ->get()
                    ->loadMorph('bookmarkItemable', [
                        Article::class => ['categories', 'tags', 'attachments', 'user'],
                    ])
                    ->pluck('bookmarkItemable')
                    ->all();
        }
        throw new Exception("unsupport type provided:{$bulkZip->bulk_zippable_type}", 1);
    }

    public function failed(Throwable $e)
    {
        report($e);
        $this->bulkZip->delete();
    }
}
