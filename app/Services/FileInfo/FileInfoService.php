<?php

declare(strict_types=1);

namespace App\Services\FileInfo;

use App\Models\Attachment;
use App\Models\Attachment\FileInfo;
use App\Repositories\Attachment\FileInfoRepository;
use Exception;

final class FileInfoService
{
    /**
     * @var array<string, array<int, \App\Services\FileInfo\Extractors\Extractor>>
     */
    private array $extractorCache = [];

    /**
     * @param  \App\Services\FileInfo\Extractors\Extractor[]  $extractors
     */
    public function __construct(
        private readonly FileInfoRepository $fileInfoRepository,
        private readonly ZipArchiveParser $zipArchiveParser,
        private readonly TextService $textService,
        private readonly array $extractors,
    ) {}

    public function updateOrCreateFromPak(Attachment $attachment): FileInfo
    {
        try {
            $filename = $attachment->original_name;

            // Check file existence before reading
            if (! file_exists($attachment->full_path)) {
                throw new Exception('File not found: '.$attachment->full_path);
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

            // Sanitize data before JSON encoding to prevent UTF-8 errors
            $data = $this->sanitizeForJson($data);

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
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // Cache extractors by extension
        if (! isset($this->extractorCache[$ext])) {
            $this->extractorCache[$ext] = array_filter(
                $this->extractors,
                fn (\App\Services\FileInfo\Extractors\Extractor $e): bool => $e->isTarget($filename)
            );
        }

        foreach ($this->extractorCache[$ext] as $extractor) {
            // Only process text for text extractors (performance optimization)
            $processedText = $extractor->isText() ? $this->handleText($text) : $text;

            $extracted = $extractor->extract($processedText);

            // Store extracted data with extractor key
            if ($extracted !== []) {
                $data[$extractor->getKey()][$filename] = $extracted;
            }
        }

        return $data;
    }

    private function handleText(string $text): string
    {
        $text = $this->textService->encoding($text);

        return $this->textService->removeBom($text);
    }

    /**
     * Recursively sanitize data to ensure valid UTF-8 for JSON encoding
     */
    private function sanitizeForJson(mixed $data): mixed
    {
        if (is_array($data)) {
            return array_map($this->sanitizeForJson(...), $data);
        }

        if (is_string($data)) {
            // If already valid UTF-8, return as-is
            if (mb_check_encoding($data, 'UTF-8')) {
                return $data;
            }

            // Force sanitization: replace invalid UTF-8 sequences
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }

        return $data;
    }
}
