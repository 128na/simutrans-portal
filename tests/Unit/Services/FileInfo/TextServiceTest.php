<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo;

use App\Services\FileInfo\TextService;
use Tests\Unit\TestCase;

class TextServiceTest extends TestCase
{
    public function test_remove_bom(): void
    {
        $text = pack('H*', 'EFBBBF').'dummy';
        $result = $this->getSUT()->removeBom($text);
        $this->assertSame('dummy', $result);
    }

    public function test_encoding(): void
    {
        $text = 'dummy';
        $result = $this->getSUT()->encoding($text);
        $this->assertSame('dummy', $result);
    }

    public function test_encoding_sjis(): void
    {
        $text = mb_convert_encoding('dummy', 'SJIS');
        $result = $this->getSUT()->encoding($text);
        $this->assertSame('dummy', $result);
    }

    private function getSUT(): TextService
    {
        return app(TextService::class);
    }
}
