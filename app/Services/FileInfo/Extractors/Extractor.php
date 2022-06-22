<?php

namespace App\Services\FileInfo\Extractors;

interface Extractor
{
    /**
     * 保存キー
     */
    public function getKey(): string;

    /**
     * 処理対象か.
     */
    public function isTarget(string $filename): bool;

    /**
     * データ抽出処理.
     *
     * @return string[]
     */
    public function extract(string $filename): array;
}
