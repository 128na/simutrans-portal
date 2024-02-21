<?php

declare(strict_types=1);

namespace App\Services\BulkZip;

use App\Exceptions\ZipErrorException;
use App\Services\BulkZip\Decorators\BaseDecorator;
use App\Services\Service;
use ErrorException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Throwable;
use ZipArchive;

class ZipManager extends Service
{
    private string $filepath;

    /**
     * @param  array<BaseDecorator>  $decorators
     */
    public function __construct(
        private ZipArchive $zipArchive,
        private Filesystem $disk,
        private array $decorators
    ) {
    }

    private function randName(?string $prefix = null, ?string $suffix = null): string
    {
        return $prefix.Str::uuid().$suffix;
    }

    private function isZipFile(string $filepath): bool
    {
        $mime = $this->disk->mimeType($filepath);

        return $mime === 'application/zip';
    }

    /**
     * @param  Model[]  $items
     */
    public function create(array $items): string
    {
        $this->filepath = $this->randName('bulk_zip/', '.zip');

        $result = $this->processItems($items);

        foreach ($result['files'] as $filename => $filepath) {
            if ($this->isZipFile($filepath)) {
                set_time_limit(60);
                $this->mergeZip($filepath, 'files/'.$filename);
            } else {
                $this->addFile($filepath, 'files/'.$filename);
            }
        }
        if (! empty($result['contents'])) {
            $this->addTextFile($result['contents']);
            $this->addCsvFile($result['contents']);
            // $this->addJsonFile($result['contents']);
        }

        return $this->filepath;
    }

    /**
     * アイテムから情報を取得.
     *
     * @param  array<Model>  $items
     * @return array<string, mixed>
     */
    private function processItems(array $items): array
    {
        $result = [
            'contents' => [
                [['ZIPファイル作成日時', now()->toDateTimeString()]],
            ],
            'files' => [],
        ];
        foreach ($items as $item) {
            foreach ($this->decorators as $decorator) {
                if ($decorator->canProcess($item)) {
                    $result = $decorator->process($result, $item);
                }
            }
        }

        return $result;
    }

    /**
     * テキストファイル.
     *
     * @param  array<array<mixed>>  $contents
     */
    private function addTextFile(array $contents): void
    {
        $content = [];

        foreach ($contents as $rows) {
            foreach ($rows as $row) {
                if (is_array($row)) {
                    $row = implode("\t", $row);
                }
                $content[] = $row;
            }
        }

        $this->addText('contents.txt', implode("\n", $content));
    }

    /**
     * SJIS csvファイル.
     *
     * @param  array<array<mixed>>  $contents
     */
    private function addCsvFile(array $contents): void
    {
        $csv = tmpfile();
        if ($csv === false) {
            throw new ZipErrorException('tmpfile faild');
        }
        foreach ($contents as $rows) {
            foreach ($rows as $row) {
                if (is_string($row)) {
                    $row = [$row];
                }
                fputcsv($csv, mb_convert_encoding($row, 'SJIS', 'UTF-8'));
            }
        }
        $filepath = $this->randName('bulk_zip/', '.csv');
        $this->disk->put($filepath, $csv);
        fclose($csv);

        $this->addFile($filepath, 'contents.csv');
        $this->disk->delete($filepath);
    }

    private function open(): void
    {
        $result = $this->zipArchive->open($this->disk->path($this->filepath), ZipArchive::CREATE);
        if ($result !== true) {
            throw new ZipErrorException("open faild: {$this->filepath}", $result);
        }
    }

    private function addFile(string $filepath, string $filenameInZip = ''): void
    {
        $this->open();
        $path = $this->disk->path($filepath);
        $result = $this->zipArchive->addFile($path, $filenameInZip);
        $this->close();

        if ($result !== true) {
            throw new ZipErrorException("add file faild: {$filepath}, {$filenameInZip}");
        }
    }

    private function mergeZip(string $filepath, string $filenameInZip = ''): void
    {
        $basedir = str_replace(basename($filenameInZip), '', $filenameInZip);
        $path = $this->disk->path($filepath);
        $z = new ZipArchive();
        try {
            $z->open($path);
            for ($i = 0; $i < $z->numFiles; $i++) {
                $name = $z->getNameIndex($i);
                if ($name === false) {
                    throw new ZipErrorException("getNameIndex faild: {$name}");
                }
                $rc = $z->getStream($name);
                if ($rc === false) {
                    throw new ZipErrorException("getStream faild: {$name}");
                }
                $randName = $this->randName();
                $this->disk->put($randName, $rc);
                $this->addFile($randName, "{$basedir}/{$name}");
                $this->disk->delete($randName);
            }
            $z->close();
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function addText(string $filenameInZip, string $content): void
    {
        $this->open();
        $result = $this->zipArchive->addFromString($filenameInZip, $content);
        $this->close();

        if ($result !== true) {
            throw new ZipErrorException("add text faild: {$filenameInZip}");
        }
    }

    private function close(): void
    {
        try {
            // CIエラー対策 ZipArchive::close(): Failure to create temporary file: No such file or directory
            $res = $this->zipArchive->close() !== true;
            if ($res) {
                throw new ZipErrorException('close faild');
            }
        } catch (ErrorException $e) {
            report($e);
        }
    }
}
