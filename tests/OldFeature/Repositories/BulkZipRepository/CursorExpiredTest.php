<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\BulkZipRepository;

use App\Models\BulkZip;
use App\Repositories\BulkZipRepository;
use Illuminate\Support\LazyCollection;
use Tests\TestCase;

class CursorExpiredTest extends TestCase
{
    private BulkZipRepository $bulkZipRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bulkZipRepository = app(BulkZipRepository::class);
    }

    public function test(): void
    {
        BulkZip::factory()->create(['created_at' => today()->modify('-1 days')]);
        $lazyCollection = $this->bulkZipRepository->cursorExpired();
        $this->assertInstanceOf(LazyCollection::class, $lazyCollection);
        $this->assertEquals(1, $lazyCollection->count());
    }

    /**
     * @dataProvider dataNotFound
     */
    public function test_含まれない(array $data): void
    {
        BulkZip::factory()->create($data);

        $lazyCollection = $this->bulkZipRepository->cursorExpired();
        $this->assertEquals(0, $lazyCollection->count());
    }

    public static function dataNotFound(): \Generator
    {
        yield '1日より新しい' => [['created_at' => now('Asia/Tokyo')]];
    }
}
