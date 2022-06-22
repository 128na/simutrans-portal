<?php

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\PakExtractor;
use Tests\UnitTestCase;

class PakExtractorTest extends UnitTestCase
{
    private function getSUT(): PakExtractor
    {
        return app(PakExtractor::class);
    }

    public function testGetKey()
    {
        $result = $this->getSUT()->getKey();
        $this->assertEquals('paks', $result);
    }

    public function testIsTarget()
    {
        $service = $this->getSUT();

        $this->assertFalse($service->isTarget('dummy'));
        $this->assertTrue($service->isTarget('dummy.pak'));
    }

    public function testExtract()
    {
        $service = $this->getSUT();

        $data = file_get_contents(__DIR__.'/vehicle.transparent_vehicle.pak');

        $result = $service->extract($data);
        $this->assertEquals(['transparent_vehicle'], $result);
    }
}
