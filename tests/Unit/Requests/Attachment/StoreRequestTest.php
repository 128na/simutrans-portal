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
    }

    public static function dataPass(): \Generator
    {
        yield '指定なしで通常ファイル' => [
            ['file' => UploadedFile::fake()->create('test.zip', 1, 'application/zip')],
        ];
    }
}
