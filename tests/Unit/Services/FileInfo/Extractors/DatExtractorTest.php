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
        $service = $this->getSUT();

        $this->assertFalse($service->isTarget('dummy'));
        $this->assertTrue($service->isTarget('dummy.dat'));
    }

    public function testExtract(): void
    {
        $service = $this->getSUT();

        $data = 'obj=building
name=hoge
type=foo
---
obj=building
name=fuga
type=bar
';

        $result = $service->extract($data);
        $this->assertEquals(['hoge', 'fuga'], $result);
    }
}
