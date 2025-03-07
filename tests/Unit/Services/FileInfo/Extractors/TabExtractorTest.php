<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\TabExtractor;
use Tests\Unit\TestCase;

final class TabExtractorTest extends TestCase
{
    public function test_get_key(): void
    {
        $result = $this->getSUT()->getKey();
        $this->assertSame('tabs', $result);
    }

    public function test_is_target(): void
    {
        $sUT = $this->getSUT();

        $this->assertFalse($sUT->isTarget('dummy'));
        $this->assertTrue($sUT->isTarget('dummy.tab'));
    }

    public function test_extract(): void
    {
        $sUT = $this->getSUT();

        $data = '§example
hoge
ほげ
# comment
fuga
ふが
';

        $result = $sUT->extract($data);
        $this->assertSame(['hoge' => 'ほげ', 'fuga' => 'ふが'], $result);
    }

    private function getSUT(): TabExtractor
    {
        return app(TabExtractor::class);
    }
}
