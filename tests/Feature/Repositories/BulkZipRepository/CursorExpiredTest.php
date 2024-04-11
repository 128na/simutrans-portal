<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\BulkZipRepository;

use App\Models\BulkZip;
use App\Repositories\BulkZipRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\LazyCollection;
use Tests\Feature\TestCase;

final class CursorExpiredTest extends TestCase
{
    private BulkZipRepository $bulkZipRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->bulkZipRepository = app(BulkZipRepository::class);
    }

    public function test(): void
    {
        $time = CarbonImmutable::create(2020, 1, 2, 3, 4, 5);
        BulkZip::factory()->create(['created_at' => $time]);
        $lazyCollection = $this->bulkZipRepository->cursorExpired($time);
        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertEquals(1, $lazyCollection->count());
    }

    public function test_含まれない(): void
    {
        $time = CarbonImmutable::create(2020, 1, 2, 3, 4, 5);
        BulkZip::factory()->create(['created_at' => $time]);

        $lazyCollection = $this->bulkZipRepository->cursorExpired($time->subSecond());
        $this->assertEquals(0, $lazyCollection->count());
    }
}
