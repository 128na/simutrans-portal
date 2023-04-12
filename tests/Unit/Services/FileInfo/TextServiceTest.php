<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo;

use App\Services\FileInfo\TextService;
use Tests\UnitTestCase;

final class TextServiceTest extends UnitTestCase
{
    private function getSUT(): TextService
    {
        return app(TextService::class);
    }

    public function testRemoveBom(): void
    {
        $text = pack('H*', 'EFBBBF').'dummy';
        $result = $this->getSUT()->removeBom($text);
        $this->assertEquals('dummy', $result);
    }

    public function testEncoding(): void
    {
        $text = 'dummy';
        $result = $this->getSUT()->encoding($text);
        $this->assertEquals('dummy', $result);
    }

    public function testEncodingSjis(): void
    {
        $text = mb_convert_encoding('dummy', 'SJIS');
        $result = $this->getSUT()->encoding($text);
        $this->assertEquals('dummy', $result);
    }
}
