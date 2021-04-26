<?php

namespace Tests\Feature\Repositories\BulkZipRepository;

use App\Models\BulkZip;
use App\Repositories\BulkZipRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class FindOrFailByUuidTest extends TestCase
{
    private BulkZipRepository $bulkZipRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bulkZipRepository = app(BulkZipRepository::class);
    }

    public function test()
    {
        $bulkZip = BulkZip::factory()->create(['generated' => true, 'path' => 'test']);
        $res = $this->bulkZipRepository->findOrFailByUuid($bulkZip->uuid);
        $this->assertEquals($bulkZip->id, $res->id);
    }

    /**
     * @dataProvider dataNotFound
     */
    public function testNotFound($data)
    {
        $this->expectException(ModelNotFoundException::class);
        $bulkZip = BulkZip::factory()->create($data);
        $res = $this->bulkZipRepository->findOrFailByUuid($bulkZip->uuid);
    }

    public function dataNotFound()
    {
        yield '未生成' => [['generated' => false, 'path' => 'test']];
        yield 'パスが空' => [['generated' => true, 'path' => null]];
        yield '1日以上前' => [['generated' => true, 'path' => 'test', 'created_at' => today()->modify('-1 days')]];
    }
}
