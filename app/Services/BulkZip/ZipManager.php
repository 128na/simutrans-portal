<?php

namespace App\Services\BulkZip;

use App\Exceptions\ZipErrorException;
use App\Services\BulkZip\Decorators\BaseDecorator;
use App\Services\Service;
use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Str;
use Throwable;
use ZipArchive;

class ZipManager extends Service
{
    private ZipArchive $zipArchive;

    private FilesystemAdapter $disk;

    private string $filepath;

    /**
     * @var BaseDecorator[]
     */
    private array $decorators;

    public function __construct(ZipArchive $zipArchive, FilesystemAdapter $disk, array $decorators)
    {
        $this->zipArchive = $zipArchive;
        $this->disk = $disk;
        $this->decorators = $decorators;
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
     * @param  Model[]  $items
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
     */
    private function addCsvFile(array $contents): void
    {
        $csv = tmpfile();
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
                $rc = $z->getStream($name);
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
