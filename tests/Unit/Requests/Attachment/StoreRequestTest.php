<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Attachment;

use App\Http\Requests\Attachment\StoreRequest;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

class StoreRequestTest extends TestCase
{
    #[DataProvider('dataFail')]
    public function test_fail(array $data, string $expectedErrorField): void
    {
        $messageBag = $this->makeValidator(StoreRequest::class, $data)->errors();
        $this->assertArrayHasKey($expectedErrorField, $messageBag->toArray());
    }

    #[DataProvider('dataPass')]
    public function test_pass(array $data): void
    {
        $messageBag = $this->makeValidator(StoreRequest::class, $data)->errors();
        $this->assertEmpty($messageBag->toArray());
    }

    public static function dataFail(): \Generator
    {
        yield 'fileが空' => [
            ['file' => null],
            'file',
        ];
        yield 'file.*がファイル以外' => [
            ['file' => 'test.zip'],
            'file',
        ];
        yield '許可されていない拡張子(.php)' => [
            ['file' => UploadedFile::fake()->create('shell.php', 1)],
            'file',
        ];
        yield '許可されていない拡張子(.exe)' => [
            ['file' => UploadedFile::fake()->create('malware.exe', 1)],
            'file',
        ];
        yield 'サイズ上限(1GB)を超える' => [
            ['file' => UploadedFile::fake()->create('test.zip', 1_000_001, 'application/zip')],
            'file',
        ];
    }

    public static function dataPass(): \Generator
    {
        yield '指定なしで通常ファイル' => [
            ['file' => UploadedFile::fake()->create('test.zip', 1, 'application/zip')],
        ];
        yield 'Simutransアドオンファイル(.pak)' => [
            ['file' => UploadedFile::fake()->create('addon.pak', 1)],
        ];
        yield '設定ファイル(.tab)' => [
            ['file' => UploadedFile::fake()->create('simuconf.tab', 1)],
        ];
        yield 'サイズ上限(1GB)以内' => [
            ['file' => UploadedFile::fake()->create('test.zip', 1_000_000, 'application/zip')],
        ];
    }
}
