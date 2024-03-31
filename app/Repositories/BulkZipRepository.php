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
class BulkZipRepository extends BaseRepository
{
    /**
     * @var BulkZip
     */
    protected $model;

    public function __construct(BulkZip $bulkZip)
    {
        $this->model = $bulkZip;
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

    public function cursorExpired(CarbonImmutable $expiredAt): LazyCollection
    {
        return $this->model
            ->where('created_at', '<=', $expiredAt)
            ->cursor();
    }
}
