<?php

namespace Tests\Unit\Services\FileInfo;

use App\Services\FileInfo\TextService;
use Tests\UnitTestCase;

class TextServiceTest extends UnitTestCase
{
    private function getSUT(): TextService
    {
        return app(TextService::class);
    }

    public function testRemoveBom()
    {
        $text = pack('H*', 'EFBBBF').'dummy';
        $result = $this->getSUT()->removeBom($text);
        $this->assertEquals('dummy', $result);
    }

    public function testEncoding()
    {
        $text = 'dummy';
        $result = $this->getSUT()->encoding($text);
        $this->assertEquals('dummy', $result);
    }

    public function testEncodingSjis()
    {
        $text = mb_convert_encoding('dummy', 'SJIS');
        $result = $this->getSUT()->encoding($text);
        $this->assertEquals('dummy', $result);
    }
}
