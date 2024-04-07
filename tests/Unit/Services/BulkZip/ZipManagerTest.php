<?php

declare(strict_types=1);

namespace Tests\Unit\Services\BulkZip;

use App\Exceptions\ZipErrorException;
use App\Services\BulkZip\Decorators\BaseDecorator;
use App\Services\BulkZip\ZipManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\Unit\TestCase;
use ZipArchive;

final class ZipManagerTest extends TestCase
{
    public function test(): void
    {
        /**
         * @var ZipArchive
         */
        $zipArchiveMock = $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->allows('open')->andReturn(true);
            $mock->allows('addFromString')->andReturn(true);
            $mock->allows('addFile')->andReturn(true);
            $mock->allows()->close()->andReturn(true);
        });
        $decoratorMock = $this->mock(BaseDecorator::class, function (MockInterface $mock): void {
            $mock->allows('canProcess')->once()->andReturn(true);
            $mock->allows('process')->once()->andReturn([
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
