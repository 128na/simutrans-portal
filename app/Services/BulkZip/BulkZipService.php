<?php

declare(strict_types=1);

namespace App\Services\BulkZip;

use App\Contracts\Models\BulkZippableInterface;
use App\Models\BulkZip;
use App\Repositories\BulkZipRepository;

final readonly class BulkZipService
{
    public function __construct(private BulkZipRepository $bulkZipRepository) {}

    public function findOrCreateAndDispatch(BulkZippableInterface $bulkZippable): BulkZip
    {
        $bulkZip = $this->bulkZipRepository->findByBulkZippable($bulkZippable);
        if (is_null($bulkZip)) {
            $bulkZip = $this->bulkZipRepository->storeByBulkZippable($bulkZippable);
            dispatch(new \App\Jobs\BulkZip\JobCreateBulkZip($bulkZip));
        }

        dispatch(new \App\Jobs\BulkZip\JobDeleteExpiredBulkzip);

        return $bulkZip;
    }
}
