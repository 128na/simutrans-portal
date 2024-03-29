<?php

declare(strict_types=1);

namespace Tests\OldUnit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\TabExtractor;
use Tests\UnitTestCase;

class TabExtractorTest extends UnitTestCase
{
    private function getSUT(): TabExtractor
    {
        return app(TabExtractor::class);
    }

    public function testGetKey(): void
    {
        $result = $this->getSUT()->getKey();
        $this->assertEquals('tabs', $result);
    }

    public function testIsTarget(): void
    {
        $sUT = $this->getSUT();

        $this->assertFalse($sUT->isTarget('dummy'));
        $this->assertTrue($sUT->isTarget('dummy.tab'));
    }

    public function testExtract(): void
    {
        $sUT = $this->getSUT();

        $data = '§example
hoge
ほげ
# comment
fuga
ふが
';

        $result = $sUT->extract($data);
        $this->assertEquals(['hoge' => 'ほげ', 'fuga' => 'ふが'], $result);
    }
}
