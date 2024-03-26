<?php

declare(strict_types=1);

namespace Tests\Unit\Services\BulkZip;

use App\Exceptions\ZipErrorException;
use App\Services\BulkZip\Decorators\BaseDecorator;
use App\Services\BulkZip\ZipManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\UnitTestCase;
use ZipArchive;

class ZipManagerTest extends UnitTestCase
{
    public function test(): void
    {
        /**
         * @var ZipArchive
         */
        $zipArchiveMock = $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->shouldReceive('open')->andReturn(true);
            $mock->shouldReceive('addFromString')->andReturn(true);
            $mock->shouldReceive('close')->andReturn(true);

            $mock->shouldReceive('open')->andReturn(true);
            $mock->shouldReceive('addFile')->andReturn(true);
            $mock->shouldReceive('close')->andReturn(true);

            $mock->shouldReceive('open')->andReturn(true);
            $mock->shouldReceive('addFile')->andReturn(true);
            $mock->shouldReceive('close')->andReturn(true);
        });
        $decoratorMock = $this->mock(BaseDecorator::class, function (MockInterface $mock): void {
            $mock->shouldReceive('canProcess')->andReturn(true);
            $mock->shouldReceive('process')->andReturn([
                'contents' => [['test']],
                'files' => [],
            ]);
        });
        $filesystem = Storage::fake();
        $modelMock = $this->mock(Model::class);

        $zipManager = new ZipManager($zipArchiveMock, $filesystem, [$decoratorMock]);
        $result = $zipManager->create([$modelMock]);

        $this->assertFalse(Storage::disk('public')->exists($result), '実際に出力されていないこと');
    }

    public function testError(): void
    {
        $this->expectException(ZipErrorException::class);

        /**
         * @var ZipArchive
         */
        $zipArchiveMock = $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->shouldReceive('open')->andReturn(ZipArchive::ER_OPEN);
        });
        $filesystem = Storage::fake();

        $zipManager = new ZipManager($zipArchiveMock, $filesystem, []);
        $zipManager->create([]);
    }
}
