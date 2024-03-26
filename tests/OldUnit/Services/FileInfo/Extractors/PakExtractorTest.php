<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\PakExtractor;
use Tests\UnitTestCase;

class PakExtractorTest extends UnitTestCase
{
    private function getSUT(): PakExtractor
    {
        return app(PakExtractor::class);
    }

    public function testGetKey(): void
    {
        $result = $this->getSUT()->getKey();
        $this->assertEquals('paks', $result);
    }

    public function testIsTarget(): void
    {
        $sUT = $this->getSUT();

        $this->assertFalse($sUT->isTarget('dummy'));
        $this->assertTrue($sUT->isTarget('dummy.pak'));
    }

    public function testExtract(): void
    {
        $sUT = $this->getSUT();

        $data = file_get_contents(__DIR__.'/vehicle.transparent_vehicle.pak');

        $result = $sUT->extract($data);
        $this->assertEquals(['transparent_vehicle'], $result);
    }
}
