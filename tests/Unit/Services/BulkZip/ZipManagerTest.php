<?php

namespace Tests\Unit\Services\BulkZip;

use App\Exceptions\ZipErrorException;
use App\Services\BulkZip\Decorators\BaseDecorator;
use App\Services\BulkZip\ZipManager;
use Illuminate\Database\Eloquent\Model;
use Mockery\MockInterface;
use Storage;
use Tests\UnitTestCase;
use ZipArchive;

class ZipManagerTest extends UnitTestCase
{
    public function test()
    {
        /**
         * @var ZipArchive
         */
        $zipArchiveMock = $this->mock(ZipArchive::class, function (MockInterface $m) {
            $m->shouldReceive('open')->andReturn(true);
            $m->shouldReceive('addFromString')->andReturn(true);
            $m->shouldReceive('close')->andReturn(true);

            $m->shouldReceive('open')->andReturn(true);
            $m->shouldReceive('addFile')->andReturn(true);
            $m->shouldReceive('close')->andReturn(true);

            $m->shouldReceive('open')->andReturn(true);
            $m->shouldReceive('addFile')->andReturn(true);
            $m->shouldReceive('close')->andReturn(true);
        });
        $decoratorMock = $this->mock(BaseDecorator::class, function (MockInterface $m) {
            $m->shouldReceive('canProcess')->andReturn(true);
            $m->shouldReceive('process')->andReturn([
                'contents' => [['test']],
                'files' => [],
            ]);
        });
        $disk = Storage::fake();
        $modelMock = $this->mock(Model::class);

        $zipManager = new ZipManager($zipArchiveMock, $disk, [$decoratorMock]);
        $result = $zipManager->create([$modelMock]);

        $this->assertFalse(Storage::disk('public')->exists($result), '実際に出力されていないこと');
    }

    public function test_error()
    {
        $this->expectException(ZipErrorException::class);

        /**
         * @var ZipArchive
         */
        $zipArchiveMock = $this->mock(ZipArchive::class, function (MockInterface $m) {
            $m->shouldReceive('open')->andReturn(ZipArchive::ER_OPEN);
        });
        $disk = Storage::fake();

        $zipManager = new ZipManager($zipArchiveMock, $disk, []);
        $zipManager->create([]);
    }
}
