<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\PakExtractor;
use Tests\Unit\TestCase;

final class PakExtractorTest extends TestCase
{
    public function test_get_key(): void
    {
        $result = $this->getSUT()->getKey();
        $this->assertSame('paks', $result);
    }

    public function test_is_target(): void
    {
        $sUT = $this->getSUT();

        $this->assertFalse($sUT->isTarget('dummy'));
        $this->assertTrue($sUT->isTarget('dummy.pak'));
    }

    public function test_extract(): void
    {
        $sUT = $this->getSUT();

        $data = file_get_contents(__DIR__.'/vehicle.transparent_vehicle.pak');

        $result = $sUT->extract($data);
        $this->assertSame(['transparent_vehicle'], $result);
    }

    private function getSUT(): PakExtractor
    {
        return app(PakExtractor::class);
    }
}
