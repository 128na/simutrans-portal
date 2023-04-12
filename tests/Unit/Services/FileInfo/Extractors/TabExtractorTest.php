<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\TabExtractor;
use Tests\UnitTestCase;

final class TabExtractorTest extends UnitTestCase
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
        $service = $this->getSUT();

        $this->assertFalse($service->isTarget('dummy'));
        $this->assertTrue($service->isTarget('dummy.tab'));
    }

    public function testExtract(): void
    {
        $service = $this->getSUT();

        $data = '§example
hoge
ほげ
# comment
fuga
ふが
';

        $result = $service->extract($data);
        $this->assertEquals(['hoge' => 'ほげ', 'fuga' => 'ふが'], $result);
    }
}
