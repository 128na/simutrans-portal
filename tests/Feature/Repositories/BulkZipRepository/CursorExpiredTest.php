<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\BulkZipRepository;

use PHPUnit\Framework\Attributes\DataProvider;
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

    public function test()
    {
        BulkZip::factory()->create(['created_at' => today()->modify('-1 days')]);
        $res = $this->bulkZipRepository->cursorExpired();
        $this->assertInstanceOf(LazyCollection::class, $res);
        $this->assertEquals(1, $res->count());
    }

    #[DataProvider('dataNotFound')]
    public function test_含まれない($data)
    {
        BulkZip::factory()->create($data);
        $res = $this->bulkZipRepository->cursorExpired();
        $this->assertEquals(0, $res->count());
    }

    public function dataNotFound()
    {
        yield '1日より新しい' => [['created_at' => now()]];
    }
}
