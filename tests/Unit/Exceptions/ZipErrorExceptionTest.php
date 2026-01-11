<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\ZipErrorException;
use Tests\Unit\TestCase;
use ZipArchive;

class ZipErrorExceptionTest extends TestCase
{
    public function test_exception_without_code(): void
    {
        $exception = new ZipErrorException('Test error message');

        $this->assertInstanceOf(ZipErrorException::class, $exception);
        $this->assertEquals('Test error message', $exception->getMessage());
    }

    public function test_exception_with_er_ok_code(): void
    {
        $exception = new ZipErrorException('Test error', ZipArchive::ER_OK);

        $this->assertInstanceOf(ZipErrorException::class, $exception);
        $this->assertStringContainsString('エラーはありません', $exception->getMessage());
    }

    public function test_exception_with_er_noent_code(): void
    {
        $exception = new ZipErrorException('File not found', ZipArchive::ER_NOENT);

        $this->assertInstanceOf(ZipErrorException::class, $exception);
        $this->assertStringContainsString('そのファイルはありません', $exception->getMessage());
    }

    public function test_exception_with_er_open_code(): void
    {
        $exception = new ZipErrorException('Cannot open', ZipArchive::ER_OPEN);

        $this->assertInstanceOf(ZipErrorException::class, $exception);
        $this->assertStringContainsString('ファイルをオープンできません', $exception->getMessage());
    }

    public function test_exception_with_er_read_code(): void
    {
        $exception = new ZipErrorException('Read error', ZipArchive::ER_READ);

        $this->assertInstanceOf(ZipErrorException::class, $exception);
        $this->assertStringContainsString('読み込みエラー', $exception->getMessage());
    }

    public function test_exception_with_er_nozip_code(): void
    {
        $exception = new ZipErrorException('Not a zip file', ZipArchive::ER_NOZIP);

        $this->assertInstanceOf(ZipErrorException::class, $exception);
        $this->assertStringContainsString('zip アーカイブではありません', $exception->getMessage());
    }

    public function test_exception_with_unknown_code(): void
    {
        $exception = new ZipErrorException('Unknown error', 99999);

        $this->assertInstanceOf(ZipErrorException::class, $exception);
        $this->assertStringContainsString('不明なエラー', $exception->getMessage());
    }
}
