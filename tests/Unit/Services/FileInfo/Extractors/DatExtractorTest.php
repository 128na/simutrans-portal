<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\DatExtractor;
use Tests\Unit\TestCase;

final class DatExtractorTest extends TestCase
{
    public function test_get_key(): void
    {
        $result = $this->getSUT()->getKey();
        $this->assertEquals('dats', $result);
    }

    public function test_is_target(): void
    {
        $sUT = $this->getSUT();

        $this->assertFalse($sUT->isTarget('dummy'));
        $this->assertTrue($sUT->isTarget('dummy.dat'));
    }

    public function test_extract(): void
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

    private function getSUT(): DatExtractor
    {
        return app(DatExtractor::class);
    }
}
