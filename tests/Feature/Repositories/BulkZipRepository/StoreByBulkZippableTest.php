<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\BulkZipRepository;

use App\Models\BulkZip;
use App\Models\User;
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
        /** @var \App\Contracts\Models\BulkZippableInterface */
        $user = User::factory()->create();
        $this->assertDatabaseMissing('bulk_zips', [
            'bulk_zippable_id' => $user->id,
            'bulk_zippable_type' => User::class,
        ]);

        $res = $this->bulkZipRepository->storeByBulkZippable($user);
        $this->assertInstanceOf(BulkZip::class, $res);

        $this->assertDatabaseHas('bulk_zips', [
            'bulk_zippable_id' => $user->id,
            'bulk_zippable_type' => User::class,
            'generated' => false,
            'path' => null,
        ]);
    }
}
