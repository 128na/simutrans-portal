<?php

declare(strict_types=1);

namespace Tests\OldUnit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\PakBinary;
use Tests\UnitTestCase;

class PakBinaryTest extends UnitTestCase
{
    private function getSUT(): PakBinary
    {
        return app(PakBinary::class, [
            'binary' => file_get_contents(__DIR__.'/vehicle.transparent_vehicle.pak'),
        ]);
    }

    public function testGet(): void
    {
        $result = $this->getSUT()->get();
        $this->assertEquals(
            file_get_contents(__DIR__.'/vehicle.transparent_vehicle.pak'),
            $result
        );
    }

    public function testSeek(): void
    {
        $this->getSUT()->seek(1);
        $this->assertTrue(true);
    }

    public function testEof(): void
    {
        $sUT = $this->getSUT();
        $this->assertFalse($sUT->eof());
        $sUT->seek(PHP_INT_MAX);
        $this->assertTrue($sUT->eof());
    }

    public function testSeekUntil(): void
    {
        $result = $this->getSUT()->seekUntil(pack('H*', '948C'));
        $this->assertEquals(105, $result);
    }
}
