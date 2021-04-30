<?php

namespace Tests\Feature\Repositories\BulkZipRepository;

use App\Models\Attachment;
use App\Models\BulkZip;
use App\Models\User\Bookmark;
use App\Repositories\BulkZipRepository;
use Tests\TestCase;
use TypeError;

class FindByBulkZippableTest extends TestCase
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
        $bulkZip = BulkZip::factory()->create(['bulk_zippable_id' => $bookmark->id, 'bulk_zippable_type' => Bookmark::class]);
        $res = $this->bulkZipRepository->findByBulkZippable($bookmark);
        $this->assertEquals($bulkZip->id, $res->id);
    }

    public function test_無いときはnull()
    {
        $bookmark = Bookmark::factory()->create();
        $res = $this->bulkZipRepository->findByBulkZippable($bookmark);
        $this->assertNull($res);
    }

    public function test_未対応モデルはNG()
    {
        $this->expectException(TypeError::class);
        $model = Attachment::factory()->create();
        $this->bulkZipRepository->findByBulkZippable($model);
    }
}
