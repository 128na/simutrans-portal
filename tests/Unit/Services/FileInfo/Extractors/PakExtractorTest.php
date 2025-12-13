<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\PakExtractor;
use Tests\Unit\TestCase;

class PakExtractorTest extends TestCase
{
    public function test_get_key(): void
    {
        $result = $this->getSUT()->getKey();
        $this->assertSame('paks_metadata', $result);
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

        $data = file_get_contents(__DIR__.'/file/test.pak');

        $result = $sUT->extract($data);

        $this->assertIsArray($result);
        // Now returns metadata array directly
        $this->assertNotEmpty($result);
    }

    public function test_invalid_file_returns_empty_array(): void
    {
        $sUT = $this->getSUT();

        // Invalid pak file (should return empty array)
        $result = $sUT->extract('invalid pak data');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    private function getSUT(): PakExtractor
    {
        return app(PakExtractor::class);
    }
}
