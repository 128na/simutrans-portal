<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\ReadmeExtractor;
use Tests\Unit\TestCase;

final class ReadmeExtractorTest extends TestCase
{
    private function getSUT(): ReadmeExtractor
    {
        return app(ReadmeExtractor::class);
    }

    public function testGetKey(): void
    {
        $result = $this->getSUT()->getKey();
        $this->assertEquals('readmes', $result);
    }

    public function testIsTarget(): void
    {
        $sUT = $this->getSUT();

        $this->assertFalse($sUT->isTarget('dummy.txt'));
        $this->assertTrue($sUT->isTarget('readme.txt'));
    }

    public function testExtract(): void
    {
        $sUT = $this->getSUT();

        $data = 'hoge';

        $result = $sUT->extract($data);
        $this->assertEquals(['hoge'], $result);
    }
}
