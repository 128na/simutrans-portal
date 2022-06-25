<?php

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\TabExtractor;
use Tests\UnitTestCase;

class TabExtractorTest extends UnitTestCase
{
    private function getSUT(): TabExtractor
    {
        return app(TabExtractor::class);
    }

    public function testGetKey()
    {
        $result = $this->getSUT()->getKey();
        $this->assertEquals('tabs', $result);
    }

    public function testIsTarget()
    {
        $service = $this->getSUT();

        $this->assertFalse($service->isTarget('dummy'));
        $this->assertTrue($service->isTarget('dummy.tab'));
    }

    public function testExtract()
    {
        $service = $this->getSUT();

        $data = '§example
hoge
ほげ
# comment
fuga
ふが
';

        $result = $service->extract($data);
        $this->assertEquals(['hoge' => 'ほげ', 'fuga' => 'ふが'], $result);
    }
}
