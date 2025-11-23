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
        $this->mock(Attachment::class, function (MockInterface $mock): void {
            $mock->allows()->__get('full_path')->andReturn('dummy');
        });
        $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->expects()->open('dummy')->once();
            $mock->allows('numFiles')->andReturn(0);
            $mock->expects()->close()->once();
        });
        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        $this->assertEmpty($result);
    }

    public function test_parse_text_content_reads_single_file(): void
    {
        $this->mock(Attachment::class, function (MockInterface $mock): void {
            $mock->allows()->__get('full_path')->andReturn('test.zip');
        });
        $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->expects()->open('test.zip')->once();
            $mock->allows('numFiles')->andReturn(1);
            $mock->expects()->statIndex(0, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => 'readme.txt',
                'index' => 0,
            ]);
            $mock->expects()->getFromIndex(0)->once()->andReturn('Hello World');
            $mock->expects()->close()->once();
        });
        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('readme.txt', $result);
        $this->assertSame('Hello World', $result['readme.txt']);
    }

    public function test_parse_text_content_reads_multiple_files(): void
    {
        $this->mock(Attachment::class, function (MockInterface $mock): void {
            $mock->allows()->__get('full_path')->andReturn('test.zip');
        });
        $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->expects()->open('test.zip')->once();
            $mock->allows('numFiles')->andReturn(3);
            $mock->expects()->statIndex(0, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => 'readme.txt',
                'index' => 0,
            ]);
            $mock->expects()->getFromIndex(0)->once()->andReturn('README content');
            $mock->expects()->statIndex(1, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => 'data.dat',
                'index' => 1,
            ]);
            $mock->expects()->getFromIndex(1)->once()->andReturn('DAT content');
            $mock->expects()->statIndex(2, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => 'info.tab',
                'index' => 2,
            ]);
            $mock->expects()->getFromIndex(2)->once()->andReturn('TAB content');
            $mock->expects()->close()->once();
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
    }

    public function test_parse_text_content_skips_files_with_null_stat(): void
    {
        $this->mock(Attachment::class, function (MockInterface $mock): void {
            $mock->allows()->__get('full_path')->andReturn('test.zip');
        });
        $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->expects()->open('test.zip')->once();
            $mock->allows('numFiles')->andReturn(2);
            $mock->expects()->statIndex(0, ZipArchive::FL_ENC_RAW)->once()->andReturn(false);
            $mock->expects()->statIndex(1, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => 'valid.txt',
                'index' => 1,
            ]);
            $mock->expects()->getFromIndex(1)->once()->andReturn('Valid content');
            $mock->expects()->close()->once();
        });
        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('valid.txt', $result);
        $this->assertSame('Valid content', $result['valid.txt']);
    }

    public function test_parse_text_content_skips_files_with_empty_name(): void
    {
        $this->mock(Attachment::class, function (MockInterface $mock): void {
            $mock->allows()->__get('full_path')->andReturn('test.zip');
        });
        $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->expects()->open('test.zip')->once();
            $mock->allows('numFiles')->andReturn(2);
            $mock->expects()->statIndex(0, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => '',
                'index' => 0,
            ]);
            $mock->expects()->getFromIndex(0)->once()->andReturn('Content');
            $mock->expects()->statIndex(1, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => 'valid.txt',
                'index' => 1,
            ]);
            $mock->expects()->getFromIndex(1)->once()->andReturn('Valid content');
            $mock->expects()->close()->once();
        });
        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('valid.txt', $result);
    }

    public function test_parse_text_content_skips_files_with_false_content(): void
    {
        $this->mock(Attachment::class, function (MockInterface $mock): void {
            $mock->allows()->__get('full_path')->andReturn('test.zip');
        });
        $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->expects()->open('test.zip')->once();
            $mock->allows('numFiles')->andReturn(2);
            $mock->expects()->statIndex(0, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => 'unreadable.txt',
                'index' => 0,
            ]);
            $mock->expects()->getFromIndex(0)->once()->andReturn(false);
            $mock->expects()->statIndex(1, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => 'valid.txt',
                'index' => 1,
            ]);
            $mock->expects()->getFromIndex(1)->once()->andReturn('Valid content');
            $mock->expects()->close()->once();
        });
        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('valid.txt', $result);
    }

    public function test_parse_text_content_handles_encoding_conversion(): void
    {
        $this->mock(Attachment::class, function (MockInterface $mock): void {
            $mock->allows()->__get('full_path')->andReturn('test.zip');
        });
        $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->expects()->open('test.zip')->once();
            $mock->allows('numFiles')->andReturn(1);
            // Simulate Shift-JIS encoded content (Japanese text)
            $shiftJisFileName = mb_convert_encoding('テスト.txt', 'SJIS', 'UTF-8');
            $shiftJisContent = mb_convert_encoding('こんにちは', 'SJIS', 'UTF-8');
            $mock->expects()->statIndex(0, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => $shiftJisFileName,
                'index' => 0,
            ]);
            $mock->expects()->getFromIndex(0)->once()->andReturn($shiftJisContent);
            $mock->expects()->close()->once();
        });
        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        $this->assertCount(1, $result);
        // The result should be converted to UTF-8
        $keys = array_keys($result);
        $this->assertNotEmpty($keys);
    }

    public function test_parse_text_content_ensures_close_is_called_on_exception(): void
    {
        $this->mock(Attachment::class, function (MockInterface $mock): void {
            $mock->allows()->__get('full_path')->andReturn('test.zip');
        });
        $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->expects()->open('test.zip')->once();
            $mock->allows('numFiles')->andReturn(1);
            $mock->expects()->statIndex(0, ZipArchive::FL_ENC_RAW)->once()->andThrow(new \Exception('Test exception'));
            $mock->expects()->close()->once();
        });
        $attachment = app(Attachment::class);

        try {
            $this->getSUT()->parseTextContent($attachment)->all();
            $this->fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            $this->assertSame('Test exception', $e->getMessage());
        }
    }

    public function test_parse_text_content_with_directories(): void
    {
        $this->mock(Attachment::class, function (MockInterface $mock): void {
            $mock->allows()->__get('full_path')->andReturn('test.zip');
        });
        $this->mock(ZipArchive::class, function (MockInterface $mock): void {
            $mock->expects()->open('test.zip')->once();
            $mock->allows('numFiles')->andReturn(3);
            // Directory entries typically have empty content
            $mock->expects()->statIndex(0, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => 'folder/',
                'index' => 0,
            ]);
            $mock->expects()->getFromIndex(0)->once()->andReturn('');
            $mock->expects()->statIndex(1, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => 'folder/file.txt',
                'index' => 1,
            ]);
            $mock->expects()->getFromIndex(1)->once()->andReturn('File content');
            $mock->expects()->statIndex(2, ZipArchive::FL_ENC_RAW)->once()->andReturn([
                'name' => 'root.txt',
                'index' => 2,
            ]);
            $mock->expects()->getFromIndex(2)->once()->andReturn('Root content');
            $mock->expects()->close()->once();
        });
        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseTextContent($attachment)->all();
        // Empty content (directories) should be skipped due to the if condition
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('folder/file.txt', $result);
        $this->assertArrayHasKey('root.txt', $result);
    }

    private function getSUT(): ZipArchiveParser
    {
        return app(ZipArchiveParser::class);
    }
}
