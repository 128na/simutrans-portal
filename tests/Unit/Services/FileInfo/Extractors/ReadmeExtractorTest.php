<?php

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\ReadmeExtractor;
use Tests\UnitTestCase;

class ReadmeExtractorTest extends UnitTestCase
{
    private function getSUT(): ReadmeExtractor
    {
        return app(ReadmeExtractor::class);
    }

    public function testGetKey()
    {
        $result = $this->getSUT()->getKey();
        $this->assertEquals('readmes', $result);
    }

    public function testIsTarget()
    {
        $service = $this->getSUT();

        $this->assertFalse($service->isTarget('dummy.txt'));
        $this->assertTrue($service->isTarget('readme.txt'));
    }

    public function testExtract()
    {
        $service = $this->getSUT();

        $data = 'hoge';

        $result = $service->extract($data);
        $this->assertEquals(['hoge'], $result);
    }
}
