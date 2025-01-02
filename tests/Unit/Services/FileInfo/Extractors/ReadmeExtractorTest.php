<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\ReadmeExtractor;
use Tests\Unit\TestCase;

final class ReadmeExtractorTest extends TestCase
{
    public function test_get_key(): void
    {
        $result = $this->getSUT()->getKey();
        $this->assertSame('readmes', $result);
    }

    public function test_is_target(): void
    {
        $sUT = $this->getSUT();

        $this->assertFalse($sUT->isTarget('dummy.txt'));
        $this->assertTrue($sUT->isTarget('readme.txt'));
    }

    public function test_extract(): void
    {
        $sUT = $this->getSUT();

        $data = 'hoge';

        $result = $sUT->extract($data);
        $this->assertSame(['hoge'], $result);
    }

    private function getSUT(): ReadmeExtractor
    {
        return app(ReadmeExtractor::class);
    }
}
