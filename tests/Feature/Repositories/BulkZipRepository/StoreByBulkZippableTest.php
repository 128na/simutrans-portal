<?php

namespace Tests\Feature\Repositories\BulkZipRepository;

use App\Models\BulkZip;
use App\Models\User\Bookmark;
use App\Repositories\BulkZipRepository;
use Tests\TestCase;

class StoreByBulkZippableTest extends TestCase
{
    private BulkZipRepository $bulkZipRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bulkZipRepository = app(BulkZipRepository::class);
    }

    public function test()
    {
        $bookmark = Bookmark::factory()->create();
        $this->assertDatabaseMissing('bulk_zips', [
            'bulk_zippable_id' => $bookmark->id,
            'bulk_zippable_type' => Bookmark::class,
        ]);

        $res = $this->bulkZipRepository->storeByBulkZippable($bookmark);
        $this->assertInstanceOf(BulkZip::class, $res);

        $this->assertDatabaseHas('bulk_zips', [
            'bulk_zippable_id' => $bookmark->id,
            'bulk_zippable_type' => Bookmark::class,
            'generated' => false,
            'path' => null,
        ]);
    }
}
