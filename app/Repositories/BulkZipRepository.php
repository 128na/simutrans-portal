<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Models\BulkZippableInterface;
use App\Models\BulkZip;
use Carbon\CarbonImmutable;
use Illuminate\Support\LazyCollection;

/**
 * @extends BaseRepository<BulkZip>
 */
final class BulkZipRepository extends BaseRepository
{
    public function __construct(BulkZip $bulkZip)
    {
        parent::__construct($bulkZip);
    }

    public function findByBulkZippable(BulkZippableInterface $bulkZippable): ?BulkZip
    {
        /** @var BulkZip|null */
        return $bulkZippable->bulkZippable()->first();
    }

    /**
     * @param  array<string>  $data
     */
    public function storeByBulkZippable(BulkZippableInterface $bulkZippable, array $data = []): BulkZip
    {
        /** @var BulkZip */
        return $bulkZippable->bulkZippable()->create($data);
    }

    /**
     * @return LazyCollection<int,BulkZip>
     */
    public function cursorExpired(CarbonImmutable $expiredAt): LazyCollection
    {
        return $this->model
            ->where('created_at', '<=', $expiredAt)
            ->cursor();
    }
}
