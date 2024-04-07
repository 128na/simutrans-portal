<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Attachment;

use App\Http\Requests\Api\Attachment\StoreRequest;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

class StoreRequestTest extends TestCase
{
    #[DataProvider('dataFail')]
    public function testFail(array $data, string $expectedErrorField): void
    {
        $messageBag = $this->makeValidator(StoreRequest::class, $data)->errors();
        $this->assertArrayHasKey($expectedErrorField, $messageBag->toArray());
    }

    #[DataProvider('dataPass')]
    public function testPass(array $data): void
    {
        $messageBag = $this->makeValidator(StoreRequest::class, $data)->errors();
        $this->assertCount(0, $messageBag->toArray());
    }

    public static function dataFail(): \Generator
    {
        yield 'filesがnull' => [
            ['files' => null],
            'files',
        ];
        yield 'filesが空' => [
            ['files' => []],
            'files',
        ];
        yield 'files.*がファイル以外' => [
            ['files' => ['test.zip']],
            'files.0',
        ];
        yield '画像のみで画像以外' => [
            ['only_image' => 1, 'files' => [UploadedFile::fake()->create('test.zip', 1, 'application/zip')]],
            'files.0',
        ];
        yield 'crop.topが129以上' => [
            ['only_image' => 1, 'crop' => ['top' => 129]],
            'crop.top',
        ];
        yield 'crop.bottomが129以上' => [
            ['only_image' => 1, 'crop' => ['bottom' => 129]],
            'crop.bottom',
        ];
        yield 'crop.leftが129以上' => [
            ['only_image' => 1, 'crop' => ['left' => 129]],
            'crop.left',
        ];
        yield 'crop.rightが129以上' => [
            ['only_image' => 1, 'crop' => ['right' => 129]],
            'crop.right',
        ];
    }

    public static function dataPass(): \Generator
    {
        yield '指定なしで通常ファイル' => [
            ['files' => [UploadedFile::fake()->create('test.zip', 1, 'application/zip')]],
        ];
        yield '画像のみで画像' => [
            ['only_image' => 1, 'files' => [UploadedFile::fake()->image('test.png', 1)]],
        ];
    }
}
