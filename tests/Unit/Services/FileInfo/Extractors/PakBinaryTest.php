<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors;

use App\Services\FileInfo\Extractors\PakBinary;
use Tests\Unit\TestCase;

final class PakBinaryTest extends TestCase
{
    public function test_get(): void
    {
        $result = $this->getSUT()->get();
        $this->assertEquals(
            file_get_contents(__DIR__.'/vehicle.transparent_vehicle.pak'),
            $result
        );
    }

    public function test_seek(): void
    {
        $this->getSUT()->seek(1);
        $this->assertTrue(true);
    }

    public function test_eof(): void
    {
        $sUT = $this->getSUT();
        $this->assertFalse($sUT->eof());
        $sUT->seek(PHP_INT_MAX);
        $this->assertTrue($sUT->eof());
    }

    public function test_seek_until(): void
    {
        $result = $this->getSUT()->seekUntil(pack('H*', '948C'));
        $this->assertSame(105, $result);
    }

    private function getSUT(): PakBinary
    {
        return app(PakBinary::class, [
            'binary' => file_get_contents(__DIR__.'/vehicle.transparent_vehicle.pak'),
        ]);
    }
}
