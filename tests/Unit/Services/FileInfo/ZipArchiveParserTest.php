<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo;

use App\Models\Attachment;
use App\Services\FileInfo\ZipArchiveParser;
use Mockery\MockInterface;
use Tests\UnitTestCase;
use ZipArchive;

class ZipArchiveParserTest extends UnitTestCase
{
    private function getSUT(): ZipArchiveParser
    {
        return app(ZipArchiveParser::class);
    }

    public function test(): void
    {
        $this->mock(Attachment::class, static function (MockInterface $mock): void {
            $mock->shouldReceive('getAttribute')->once()->andReturn('dummy');
        });
        $this->mock(ZipArchive::class, static function (MockInterface $mock): void {
            $mock->shouldReceive('open')->once();
            $mock->shouldReceive('close')->once();
        });
        $attachment = app(Attachment::class);
        $this->getSUT()->parseTextContent($attachment)->toArray();
    }
}
