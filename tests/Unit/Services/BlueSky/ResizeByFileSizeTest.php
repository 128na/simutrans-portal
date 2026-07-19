<?php

declare(strict_types=1);

namespace App\Services\BlueSky;

use Tests\Unit\Services\BlueSky\ResizeByFileSizeTest;

/**
 * filesize() が false を返すケース（権限エラー・競合状態等）を再現するための
 * namespace override。ResizeByFileSize.php 内の未修飾 filesize() 呼び出しは、
 * PHP の名前空間解決規則により現在の名前空間（App\Services\BlueSky）内の関数を
 * 優先して探すため、ここで定義するとテスト対象コードから見た filesize() を
 * 差し替えられる。テストが明示的に有効化した場合のみ false を返し、
 * それ以外は実際の filesize() へフォールバックする（本番挙動には影響しない）。
 */
function filesize(string $filename): false|int
{
    if (ResizeByFileSizeTest::$forceFilesizeFalse) {
        return false;
    }

    return \filesize($filename);
}

namespace Tests\Unit\Services\BlueSky;

use App\Services\BlueSky\ResizeByFileSize;
use App\Services\BlueSky\ResizeFailedException;
use Tests\Unit\TestCase;

class ResizeByFileSizeTest extends TestCase
{
    public static bool $forceFilesizeFalse = false;

    protected function tearDown(): void
    {
        self::$forceFilesizeFalse = false;
        parent::tearDown();
    }

    public function test_throws_resize_failed_exception_when_filesize_returns_false(): void
    {
        self::$forceFilesizeFalse = true;

        $tmpFile = tempnam(sys_get_temp_dir(), 'resize_test_');
        $this->assertIsString($tmpFile);
        file_put_contents($tmpFile, 'dummy content');

        try {
            $this->expectException(ResizeFailedException::class);

            (new ResizeByFileSize)($tmpFile, 1000);
        } finally {
            @unlink($tmpFile);
        }
    }

    public function test_throws_resize_failed_exception_when_input_file_is_zero_bytes(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'resize_test_');
        $this->assertIsString($tmpFile);

        try {
            $this->expectException(ResizeFailedException::class);

            (new ResizeByFileSize)($tmpFile, 1000);
        } finally {
            @unlink($tmpFile);
        }
    }

    public function test_returns_original_path_when_file_is_smaller_than_target(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'resize_test_');
        $this->assertIsString($tmpFile);
        file_put_contents($tmpFile, 'dummy content');

        try {
            $result = (new ResizeByFileSize)($tmpFile, 1_000_000);

            $this->assertSame($tmpFile, $result);
        } finally {
            @unlink($tmpFile);
        }
    }
}
