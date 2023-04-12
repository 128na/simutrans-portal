<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo;

use App\Models\Attachment;
use App\Services\FileInfo\ZipArchiveParser;
use Mockery\MockInterface;
use Tests\UnitTestCase;
use ZipArchive;

final class ZipArchiveParserTest extends UnitTestCase
{
    private function getSUT(): ZipArchiveParser
    {
        return app(ZipArchiveParser::class);
    }

    public function test(): void
    {
        $this->mock(Attachment::class, function (MockInterface $m) {
            $m->shouldReceive('getAttribute')->once()->andReturn('dummy');
        });
        $this->mock(ZipArchive::class, function (MockInterface $m) {
            $m->shouldReceive('open')->once();
            $m->shouldReceive('close')->once();
        });
        $attachment = app(Attachment::class);
        $this->getSUT()->parseTextContent($attachment)->toArray();
    }
}
