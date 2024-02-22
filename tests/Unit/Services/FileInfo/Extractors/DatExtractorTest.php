<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\DatExtractor;
use Tests\UnitTestCase;

class DatExtractorTest extends UnitTestCase
{
    private function getSUT(): DatExtractor
    {
        return app(DatExtractor::class);
    }

    public function testGetKey(): void
    {
        $result = $this->getSUT()->getKey();
        $this->assertEquals('dats', $result);
    }

    public function testIsTarget(): void
    {
        $sUT = $this->getSUT();

        $this->assertFalse($sUT->isTarget('dummy'));
        $this->assertTrue($sUT->isTarget('dummy.dat'));
    }

    public function testExtract(): void
    {
        $sUT = $this->getSUT();

        $data = 'obj=building
name=hoge
type=foo
---
obj=building
name=fuga
type=bar
';

        $result = $sUT->extract($data);
        $this->assertEquals(['hoge', 'fuga'], $result);
    }
}
