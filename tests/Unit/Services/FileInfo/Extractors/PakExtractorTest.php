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

        $data = file_get_contents(__DIR__ . '/file/test-60.8.pak');

        $result = $sUT->extract($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('names', $result);
        $this->assertArrayHasKey('metadata', $result);
        $this->assertContains('test_1', $result['names']);
        $this->assertIsArray($result['metadata']);
    }

    public function test_fallback_on_invalid_file(): void
    {
        $sUT = $this->getSUT();

        // Invalid pak file (should trigger fallback)
        $result = $sUT->extract('invalid pak data');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('names', $result);
        $this->assertArrayHasKey('metadata', $result);
        $this->assertIsArray($result['names']);
        $this->assertEmpty($result['metadata']);
    }

    private function getSUT(): PakExtractor
    {
        return app(PakExtractor::class);
    }
}
