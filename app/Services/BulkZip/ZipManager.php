<?php

namespace App\Services\BulkZip;

use App\Exceptions\ZipErrorException;
use App\Services\Service;
use App\Services\ZipDecorators\BaseDecorator;
use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Str;
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

    private function randName(?string $prefix, ?string $suffix): string
    {
        return $prefix.Str::uuid().$suffix;
    }

    /**
     * @param Model[] $items
     */
    public function create(array $items): string
    {
        $this->filepath = $this->randName('bulk_zip/', '.zip');

        $result = $this->processItems($items);

        foreach ($result['files'] as $filename => $filepath) {
            $this->addFile($filepath, 'files/'.$filename);
        }
        if (!empty($result['contents'])) {
            $this->addTextFile($result['contents']);
            $this->addCsvFile($result['contents']);
            // $this->addJsonFile($result['contents']);
        }

        return $this->filepath;
    }

    /**
     * アイテムから情報を取得.
     *
     * @param Model[] $items
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

    /**
     * jsonファイル.
     */
    private function addJsonFile(array $contents): void
    {
        $opt = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
        $this->addText('contents.json', json_encode($contents, $opt));
    }

    private function open(): void
    {
        $result = $this->zipArchive->open($this->disk->path($this->filepath), ZipArchive::CREATE);
        if ($result !== true) {
            throw new ZipErrorException("open faild: {$this->filepath}, {$result}");
        }
    }

    private function addFile(string $filepath, string $filenameInZip = ''): void
    {
        $this->open();
        $path = $this->disk->path($filepath);
        logger('addFile', [$path, $filenameInZip]);
        $result = $this->zipArchive->addFile($path, $filenameInZip);
        $this->close();

        if ($result !== true) {
            throw new ZipErrorException("add file faild: {$filepath}, {$filenameInZip}");
        }
    }

    private function addText(string $filenameInZip, string $content): void
    {
        $this->open();
        logger('addText', [$filenameInZip]);
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
