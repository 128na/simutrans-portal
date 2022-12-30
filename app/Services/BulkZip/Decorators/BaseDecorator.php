<?php

declare(strict_types=1);

namespace App\Services\BulkZip\Decorators;

use Illuminate\Database\Eloquent\Model;

abstract class BaseDecorator
{
    /**
     * このデコレーターの処理対象か.
     */
    abstract public function canProcess(Model $model): bool;

    /**
     * Zip格納データに変換する.
     *
     * @param  array<array<mixed>>  $result
     * @return array<array<mixed>>
     */
    abstract public function process(array $result, Model $model): array;

    /**
     * ファイルを追加.
     *
     * @param  array<array<mixed>>  $result
     * @return array<array<mixed>>
     */
    protected function addFile(array $result, string $filename, string $filepath): array
    {
        $result['files'][$filename] = $filepath;

        return $result;
    }

    /**
     * テキストコンテンツを追加.
     *
     * @param  array<array<mixed>>  $result
     * @param  array<mixed>  $content
     * @return array<array<mixed>>
     */
    protected function addContent(array $result, array $content): array
    {
        $result['contents'][] = $content;

        return $result;
    }

    /**
     * Zip内のファイルパス.
     */
    protected function toPath(int $id, string $name): string
    {
        return sprintf('%d/%s', $id, $name);
    }
}
