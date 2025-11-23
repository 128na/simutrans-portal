<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo;

use App\Models\Attachment;
use App\Services\FileInfo\ZipArchiveParser;
use Mockery\MockInterface;
use Tests\Unit\TestCase;
use ZipArchive;

final class ZipArchiveParserTest extends TestCase
{
    public function test_parse_text_content_with_empty_zip(): void
    {
        $zipPath = $this->createTempZip(['dummy.txt' => '']);  // Add at least one file to make it valid

        $this->mock(Attachment::class, function (MockInterface $mock) use ($zipPath): void {
            $mock->shouldReceive('getAttribute')->with('full_path')->andReturn($zipPath);
        });

        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        // Empty content should be skipped, so result should still be empty
        $this->assertEmpty($result);

        unlink($zipPath);
    }

    public function test_parse_text_content_reads_single_file(): void
    {
        $zipPath = $this->createTempZip(['readme.txt' => 'Hello World']);

        $this->mock(Attachment::class, function (MockInterface $mock) use ($zipPath): void {
            $mock->shouldReceive('getAttribute')->with('full_path')->andReturn($zipPath);
        });

        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('readme.txt', $result);
        $this->assertSame('Hello World', $result['readme.txt']);

        unlink($zipPath);
    }

    public function test_parse_text_content_reads_multiple_files(): void
    {
        $zipPath = $this->createTempZip([
            'readme.txt' => 'README content',
            'data.dat' => 'DAT content',
            'info.tab' => 'TAB content',
        ]);

        $this->mock(Attachment::class, function (MockInterface $mock) use ($zipPath): void {
            $mock->shouldReceive('getAttribute')->with('full_path')->andReturn($zipPath);
        });

        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        $this->assertCount(3, $result);
        $this->assertArrayHasKey('readme.txt', $result);
        $this->assertSame('README content', $result['readme.txt']);
        $this->assertArrayHasKey('data.dat', $result);
        $this->assertSame('DAT content', $result['data.dat']);
        $this->assertArrayHasKey('info.tab', $result);
        $this->assertSame('TAB content', $result['info.tab']);

        unlink($zipPath);
    }

    public function test_parse_text_content_skips_files_with_empty_content(): void
    {
        $zipPath = $this->createTempZip([
            'empty.txt' => '',
            'valid.txt' => 'Valid content',
        ]);

        $this->mock(Attachment::class, function (MockInterface $mock) use ($zipPath): void {
            $mock->shouldReceive('getAttribute')->with('full_path')->andReturn($zipPath);
        });

        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('valid.txt', $result);
        $this->assertSame('Valid content', $result['valid.txt']);

        unlink($zipPath);
    }

    public function test_parse_text_content_handles_encoding_conversion(): void
    {
        // Create Shift-JIS encoded content (Japanese text)
        $shiftJisFileName = mb_convert_encoding('テスト.txt', 'SJIS', 'UTF-8');
        $shiftJisContent = mb_convert_encoding('こんにちは', 'SJIS', 'UTF-8');

        $zipPath = $this->createTempZip([$shiftJisFileName => $shiftJisContent]);

        $this->mock(Attachment::class, function (MockInterface $mock) use ($zipPath): void {
            $mock->shouldReceive('getAttribute')->with('full_path')->andReturn($zipPath);
        });

        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        $this->assertCount(1, $result);
        // The result should be converted to UTF-8
        $keys = array_keys($result);
        $this->assertNotEmpty($keys);
        // Check that the content was converted to UTF-8
        $this->assertStringContainsString('こんにちは', reset($result));

        unlink($zipPath);
    }

    public function test_parse_text_content_with_directories(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_zip_').'.zip';
        $zip = new ZipArchive;
        $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Add directory (empty entry)
        $zip->addEmptyDir('folder');
        // Add files
        $zip->addFromString('folder/file.txt', 'File content');
        $zip->addFromString('root.txt', 'Root content');

        $zip->close();

        $this->mock(Attachment::class, function (MockInterface $mock) use ($tempFile): void {
            $mock->shouldReceive('getAttribute')->with('full_path')->andReturn($tempFile);
        });

        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        // Empty content (directories) should be skipped due to the if condition
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('folder/file.txt', $result);
        $this->assertArrayHasKey('root.txt', $result);

        unlink($tempFile);
    }

    private function createTempZip(array $files): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_zip_').'.zip';
        $zip = new ZipArchive;
        $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($files as $name => $content) {
            $zip->addFromString($name, $content);
        }

        $zip->close();

        return $tempFile;
    }

    private function getSUT(): ZipArchiveParser
    {
        return app(ZipArchiveParser::class);
    }
}
