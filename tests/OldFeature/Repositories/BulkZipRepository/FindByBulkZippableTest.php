<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories\BulkZipRepository;

use App\Models\Attachment;
use App\Models\BulkZip;
use App\Models\User;
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

    public function test(): void
    {
        /** @var \App\Contracts\Models\BulkZippableInterface */
        $user = User::factory()->create();
        $bulkZip = BulkZip::factory()->create(['bulk_zippable_id' => $user->id, 'bulk_zippable_type' => User::class]);
        $res = $this->bulkZipRepository->findByBulkZippable($user);
        $this->assertEquals($bulkZip->id, $res->id);
    }

    public function test無いときはnull(): void
    {
        /** @var \App\Contracts\Models\BulkZippableInterface */
        $user = User::factory()->create();
        $res = $this->bulkZipRepository->findByBulkZippable($user);
        $this->assertNull($res);
    }

    public function test未対応モデルはNG(): void
    {
        $this->expectException(TypeError::class);
        /** @var \App\Contracts\Models\BulkZippableInterface */
        $model = Attachment::factory()->create();
        $this->bulkZipRepository->findByBulkZippable($model);
    }
}
