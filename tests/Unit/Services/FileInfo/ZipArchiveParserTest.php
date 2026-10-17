<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo;

use App\Models\Attachment;
use App\Repositories\Attachment\FileInfoRepository;
use App\Services\FileInfo\FileInfoService;
use App\Services\FileInfo\TextService;
use App\Services\FileInfo\ZipArchiveParser;
use App\Services\FileInfo\ZipResourceLimitException;
use Mockery\MockInterface;
use Tests\Unit\TestCase;
use ZipArchive;

class ZipArchiveParserTest extends TestCase
{
    public function test_zip_budgets_accept_exact_boundaries(): void
    {
        config(['fileinfo.zip.max_entry_bytes' => 4, 'fileinfo.zip.max_total_bytes' => 8, 'fileinfo.zip.max_entries' => 2]);
        $path = $this->createTempZip(['a.txt' => 'aaaa', 'b.txt' => 'bbbb']);
        try {
            $attachment = \Mockery::mock(Attachment::class);
            $attachment->shouldReceive('getAttribute')->with('full_path')->andReturn($path);
            $this->assertCount(2, $this->getSUT()->parseContent($attachment)->all());
        } finally {
            unlink($path);
        }
    }

    public function test_zip_budgets_reject_one_byte_or_entry_over_limits(): void
    {
        foreach ([
            ['max_entry_bytes', 3, ['a.txt' => 'aaaa']],
            ['max_total_bytes', 7, ['a.txt' => 'aaaa', 'b.txt' => 'bbbb']],
            ['max_entries', 1, ['a.txt' => '', 'b.txt' => '']],
        ] as [$key, $limit, $files]) {
            config(['fileinfo.zip.max_entry_bytes' => 64, 'fileinfo.zip.max_total_bytes' => 256, 'fileinfo.zip.max_entries' => 10]);
            config(['fileinfo.zip.'.$key => $limit]);
            $path = $this->createTempZip($files);
            try {
                $attachment = \Mockery::mock(Attachment::class);
                $attachment->shouldReceive('getAttribute')->with('full_path')->andReturn($path);
                try {
                    $this->getSUT()->parseContent($attachment)->all();
                    $this->fail('Expected ZIP resource rejection: '.$key);
                } catch (ZipResourceLimitException) {
                    $this->assertTrue(true);
                }
            } finally {
                unlink($path);
            }
        }
    }

    public function test_oversized_declared_size_is_rejected_before_read_and_closes_archive(): void
    {
        config(['fileinfo.zip.max_entry_bytes' => 4]);
        $zip = $this->mockArchiveWithEntries(1);
        $zip->shouldReceive('open')->once()->andReturn(true);
        $zip->shouldReceive('statIndex')->once()->andReturn(['name' => 'a.txt', 'size' => 5]);
        $zip->shouldNotReceive('getFromIndex');
        $zip->shouldReceive('close')->once()->passthru();
        $attachment = \Mockery::mock(Attachment::class);
        $attachment->shouldReceive('getAttribute')->with('full_path')->andReturn('unused.zip');
        $this->expectException(ZipResourceLimitException::class);
        (new ZipArchiveParser($zip))->parseContent($attachment)->all();
    }

    public function test_understated_size_uses_bounded_read_and_closes_archive(): void
    {
        config(['fileinfo.zip.max_entry_bytes' => 4]);
        $zip = $this->mockArchiveWithEntries(1);
        $zip->shouldReceive('open')->once()->andReturn(true);
        $zip->shouldReceive('statIndex')->twice()->andReturn(['name' => 'a.txt', 'size' => 1]);
        $zip->shouldReceive('getFromIndex')->once()->with(0, 5)->andReturn('aaaaa');
        $zip->shouldReceive('close')->once()->passthru();
        $attachment = \Mockery::mock(Attachment::class);
        $attachment->shouldReceive('getAttribute')->with('full_path')->andReturn('unused.zip');
        $this->expectException(ZipResourceLimitException::class);
        (new ZipArchiveParser($zip))->parseContent($attachment)->all();
    }

    public function test_actual_total_budget_bounds_subsequent_reads(): void
    {
        config(['fileinfo.zip.max_entry_bytes' => 4, 'fileinfo.zip.max_total_bytes' => 7]);
        $zip = $this->mockArchiveWithEntries(2);
        $zip->shouldReceive('open')->once()->andReturn(true);
        $zip->shouldReceive('statIndex')->with(0, ZipArchive::FL_ENC_RAW)->twice()->andReturn(['name' => 'a.txt', 'size' => 1]);
        $zip->shouldReceive('statIndex')->with(1, ZipArchive::FL_ENC_RAW)->twice()->andReturn(['name' => 'b.txt', 'size' => 1]);
        $zip->shouldReceive('getFromIndex')->once()->with(0, 5)->andReturn('aaaa');
        $zip->shouldReceive('getFromIndex')->once()->with(1, 4)->andReturn('bbbb');
        $zip->shouldReceive('close')->once()->passthru();
        $attachment = \Mockery::mock(Attachment::class);
        $attachment->shouldReceive('getAttribute')->with('full_path')->andReturn('unused.zip');
        $repository = \Mockery::mock(FileInfoRepository::class);
        $repository->shouldNotReceive('updateOrCreate');
        $service = new FileInfoService($repository, new ZipArchiveParser($zip), new TextService, []);

        $this->expectException(ZipResourceLimitException::class);
        $service->updateOrCreateFromZip($attachment);
    }

    public function test_declared_total_is_checked_before_any_content_read(): void
    {
        config(['fileinfo.zip.max_entry_bytes' => 4, 'fileinfo.zip.max_total_bytes' => 7]);
        $zip = $this->mockArchiveWithEntries(2);
        $zip->shouldReceive('open')->once()->andReturn(true);
        $zip->shouldReceive('statIndex')->with(0, ZipArchive::FL_ENC_RAW)->once()->andReturn(['name' => 'a.txt', 'size' => 4]);
        $zip->shouldReceive('statIndex')->with(1, ZipArchive::FL_ENC_RAW)->once()->andReturn(['name' => 'b.txt', 'size' => 4]);
        $zip->shouldNotReceive('getFromIndex');
        $zip->shouldReceive('close')->once()->passthru();
        $attachment = \Mockery::mock(Attachment::class);
        $attachment->shouldReceive('getAttribute')->with('full_path')->andReturn('unused.zip');
        $this->expectException(ZipResourceLimitException::class);
        (new ZipArchiveParser($zip))->parseContent($attachment)->all();
    }

    public function test_read_failure_after_valid_entry_does_not_save_partial_metadata(): void
    {
        $zip = $this->mockArchiveWithEntries(2);
        $zip->shouldReceive('open')->once()->andReturn(true);
        $zip->shouldReceive('getFromIndex')->with(0, \Mockery::type('int'))->once()->andReturn('valid');
        $zip->shouldReceive('getFromIndex')->with(1, \Mockery::type('int'))->once()->andReturn(false);
        $zip->shouldReceive('close')->once()->passthru();
        $attachment = \Mockery::mock(Attachment::class);
        $attachment->shouldReceive('getAttribute')->with('full_path')->andReturn('unused.zip');
        $repository = \Mockery::mock(FileInfoRepository::class);
        $repository->shouldNotReceive('updateOrCreate');
        $service = new FileInfoService($repository, new ZipArchiveParser($zip), new TextService, []);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to read ZIP entry.');
        $service->updateOrCreateFromZip($attachment);
    }

    public function test_invalid_config_uses_finite_default_budgets(): void
    {
        foreach ([0, -1, PHP_INT_MAX, 'unlimited', null, INF] as $invalid) {
            config(['fileinfo.zip.max_entry_bytes' => $invalid, 'fileinfo.zip.max_total_bytes' => $invalid, 'fileinfo.zip.max_entries' => $invalid]);
            $zip = $this->mockArchiveWithEntries(1);
            $zip->shouldReceive('open')->once()->andReturn(true);
            $zip->shouldReceive('getFromIndex')->with(0, 64 * 1024 * 1024 + 1)->once()->andReturn('a');
            $zip->shouldReceive('close')->once()->passthru();
            $attachment = \Mockery::mock(Attachment::class);
            $attachment->shouldReceive('getAttribute')->with('full_path')->andReturn('unused.zip');
            $this->assertCount(1, (new ZipArchiveParser($zip))->parseContent($attachment)->all());
        }
    }

    public function test_invalid_declared_sizes_are_rejected_before_read(): void
    {
        foreach ([false, ['name' => 'a.txt', 'size' => -1], ['name' => 'a.txt']] as $stat) {
            $zip = $this->mockArchiveWithEntries(1);
            $zip->shouldReceive('open')->once()->andReturn(true);
            $zip->shouldReceive('statIndex')->once()->andReturn($stat);
            $zip->shouldNotReceive('getFromIndex');
            $zip->shouldReceive('close')->once()->passthru();
            $attachment = \Mockery::mock(Attachment::class);
            $attachment->shouldReceive('getAttribute')->with('full_path')->andReturn('unused.zip');
            try {
                (new ZipArchiveParser($zip))->parseContent($attachment)->all();
                $this->fail('Expected invalid ZIP metadata to be rejected.');
            } catch (ZipResourceLimitException) {
                $this->assertTrue(true);
            }
        }
    }

    public function test_stat_failure_on_second_pass_rejects_archive(): void
    {
        $zip = $this->mockArchiveWithEntries(1);
        $zip->shouldReceive('open')->once()->andReturn(true);
        $zip->shouldReceive('statIndex')->twice()->andReturn(['name' => 'a.txt', 'size' => 1], false);
        $zip->shouldNotReceive('getFromIndex');
        $zip->shouldReceive('close')->once()->passthru();
        $attachment = \Mockery::mock(Attachment::class);
        $attachment->shouldReceive('getAttribute')->with('full_path')->andReturn('unused.zip');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to inspect ZIP entry.');
        (new ZipArchiveParser($zip))->parseContent($attachment)->all();
    }

    public function test_parse_text_content_with_empty_zip(): void
    {
        $zipPath = $this->createTempZip(['dummy.txt' => '']);  // Add at least one file to make it valid

        $this->mock(Attachment::class, function (MockInterface $mock) use ($zipPath): void {
            $mock->shouldReceive('getAttribute')->with('full_path')->andReturn($zipPath);
        });

        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseContent($attachment)->all();
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
        $result = $this->getSUT()->parseContent($attachment)->all();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('readme.txt', $result);
        $this->assertSame('Hello World', $result['readme.txt']['content']);
        $this->assertFalse($result['readme.txt']['is_binary']);

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
        $result = $this->getSUT()->parseContent($attachment)->all();
        $this->assertCount(3, $result);
        $this->assertArrayHasKey('readme.txt', $result);
        $this->assertSame('README content', $result['readme.txt']['content']);
        $this->assertArrayHasKey('data.dat', $result);
        $this->assertSame('DAT content', $result['data.dat']['content']);
        $this->assertArrayHasKey('info.tab', $result);
        $this->assertSame('TAB content', $result['info.tab']['content']);

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
        $result = $this->getSUT()->parseContent($attachment)->all();
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('valid.txt', $result);
        $this->assertSame('Valid content', $result['valid.txt']['content']);

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
        $result = $this->getSUT()->parseContent($attachment)->all();
        $this->assertCount(1, $result);
        // The result should be converted to UTF-8
        $keys = array_keys($result);
        $this->assertNotEmpty($keys);
        // Check that the content was converted to UTF-8
        $firstResult = reset($result);
        $this->assertStringContainsString('こんにちは', $firstResult['content']);

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
        $result = $this->getSUT()->parseContent($attachment)->all();
        // Empty content (directories) should be skipped due to the if condition
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('folder/file.txt', $result);
        $this->assertArrayHasKey('root.txt', $result);

        unlink($tempFile);
    }

    /**
     * .pak/.bmp/.png など非テキスト拡張子は is_binary=true でコンテンツ無変換であることを確認する。
     */
    public function test_binary_files_are_not_encoding_converted(): void
    {
        $binaryContent = "\x42\x4D\xFF\xFE\x00\x00"; // BMP マジックバイト
        $zipPath = $this->createTempZip([
            'image.bmp' => $binaryContent,
            'sound.wav' => $binaryContent,
            'object.pak' => $binaryContent,
        ]);

        $this->mock(Attachment::class, function (MockInterface $mock) use ($zipPath): void {
            $mock->shouldReceive('getAttribute')->with('full_path')->andReturn($zipPath);
        });

        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseContent($attachment)->all();

        $this->assertCount(3, $result);
        foreach (['image.bmp', 'sound.wav', 'object.pak'] as $name) {
            $this->assertTrue($result[$name]['is_binary'], "{$name} should be binary");
            $this->assertSame($binaryContent, $result[$name]['content'], "{$name} content should not be converted");
        }

        unlink($zipPath);
    }

    /**
     * 未知の拡張子はバイナリ扱いになることを確認する。
     */
    public function test_unknown_extension_is_treated_as_binary(): void
    {
        $zipPath = $this->createTempZip(['file.xyz' => 'some content']);

        $this->mock(Attachment::class, function (MockInterface $mock) use ($zipPath): void {
            $mock->shouldReceive('getAttribute')->with('full_path')->andReturn($zipPath);
        });

        $attachment = app(Attachment::class);
        $result = $this->getSUT()->parseContent($attachment)->all();

        $this->assertCount(1, $result);
        $this->assertTrue($result['file.xyz']['is_binary']);

        unlink($zipPath);
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

    private function mockArchiveWithEntries(int $count): ZipArchive
    {
        $files = [];
        for ($i = 0; $i < $count; $i++) {
            $files[$i.'.txt'] = 'a';
        }
        $path = $this->createTempZip($files);
        $zip = \Mockery::mock(ZipArchive::class)->makePartial();
        $zip->open($path);
        $this->beforeApplicationDestroyed(function () use ($path): void {
            unlink($path);
        });

        return $zip;
    }

    private function getSUT(): ZipArchiveParser
    {
        return app(ZipArchiveParser::class);
    }
}
