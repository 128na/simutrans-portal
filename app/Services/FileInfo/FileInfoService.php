<?php

declare(strict_types=1);

namespace App\Services\FileInfo;

use App\Models\Attachment;
use App\Models\Attachment\FileInfo;
use App\Repositories\Attachment\FileInfoRepository;
use Exception;

final readonly class FileInfoService
{
    /**
     * @param  \App\Services\FileInfo\Extractors\Extractor[]  $extractors
     */
    public function __construct(
        private FileInfoRepository $fileInfoRepository,
        private ZipArchiveParser $zipArchiveParser,
        private TextService $textService,
        private array $extractors,
    ) {}

    public function updateOrCreateFromPak(Attachment $attachment): FileInfo
    {
        try {
            $filename = $attachment->original_name;

            // Check file existence before reading
            if (!file_exists($attachment->full_path)) {
                throw new Exception('File not found: ' . $attachment->full_path);
            }

            $text = file_get_contents($attachment->full_path);
            if ($text === false) {
                throw new Exception('failed file read');
            }

            $data = $this->handleExtractors($filename, $text, []);

            return $this->fileInfoRepository->updateOrCreate(['attachment_id' => $attachment->id], ['data' => $data]);
        } catch (InvalidEncodingException) {
            return $this->fileInfoRepository->updateOrCreate(['attachment_id' => $attachment->id], ['data' => []]);
        }
    }

    public function updateOrCreateFromZip(Attachment $attachment): FileInfo
    {
        try {
            $data = [];

            $contentCursor = $this->zipArchiveParser->parseContent($attachment);
            foreach ($contentCursor as $filename => $fileData) {
                $content = $fileData['content'];

                // Skip BOM removal for binary files (performance optimization)
                if (! $fileData['is_binary']) {
                    $content = $this->textService->removeBom($content);
                }

                $data = $this->handleExtractors($filename, $content, $data);
            }

            return $this->fileInfoRepository->updateOrCreate(['attachment_id' => $attachment->id], ['data' => $data]);
        } catch (InvalidEncodingException) {
            return $this->fileInfoRepository->updateOrCreate(['attachment_id' => $attachment->id], ['data' => []]);
        }
    }

    /**
     * @param  array<string, array<string, mixed>>  $data
     * @return array<string, array<string, mixed>>
     */
    private function handleExtractors(string $filename, string $text, array $data): array
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->isTarget($filename)) {
                // Only process text for text extractors (performance optimization)
                $processedText = $extractor->isText() ? $this->handleText($text) : $text;

                $extracted = $extractor->extract($processedText);

                // PakExtractor returns ['names' => [...], 'metadata' => [...]]
                if ($extractor instanceof Extractors\PakExtractor) {
                    if (isset($extracted['names'])) {
                        $data[$extractor->getKey()][$filename] = $extracted['names'];
                    }

                    if (isset($extracted['metadata']) && $extracted['metadata'] !== []) {
                        $data['paks_metadata'][$filename] = $extracted['metadata'];
                    }
                } else {
                    $data[$extractor->getKey()][$filename] = $extracted;
                }
            }
        }

        return $data;
    }

    private function handleText(string $text): string
    {
        $text = $this->textService->encoding($text);

        return $this->textService->removeBom($text);
    }
}
