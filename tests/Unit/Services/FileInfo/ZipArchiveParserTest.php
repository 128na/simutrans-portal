<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo;

use App\Models\Attachment;
use App\Services\FileInfo\ZipArchiveParser;
use Mockery\MockInterface;
use Tests\Unit\TestCase;
use ZipArchive;

final class ZipArchiveParserTest extends TestCase
{
    public function test(): void
    {
        $this->mock(Attachment::class, function (MockInterface $mock): void {
            $mock->expects()->getAttribute('full_path')->once()->andReturn('dummy');
        });
        $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->expects()->open('dummy')->once();
            $mock->expects()->close()->once();
        });
        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        $this->assertEmpty($result);
    }

    private function getSUT(): ZipArchiveParser
    {
        return app(ZipArchiveParser::class);
    }
}
