<?php

namespace App\Services\FileInfo;

use App\Models\Attachment;
use App\Models\Attachment\FileInfo;
use App\Repositories\Attachment\FileInfoRepository;
use App\Repositories\AttachmentRepository;
use App\Services\Service;

class FileInfoService extends Service
{
    /**
     * @param Extractor[] $extractors
     */
    public function __construct(
        private AttachmentRepository $attachmentRepository,
        private FileInfoRepository $fileInfoRepository,
        private ZipArchiveParser $zipArchiveParser,
        private TextService $textService,
        private array $extractors,
    ) {
    }

    public function updateOrCreateFromPak(Attachment $attachment): FileInfo
    {
        try {
            $filename = $attachment->original_name;
            $text = file_get_contents($attachment->full_path);
            $data = $this->handleExtractors($filename, $text, []);

            return $this->fileInfoRepository->updateOrCreate(['attachment_id' => $attachment->id], ['data' => $data]);
        } catch (InvalidEncodingException $e) {
            return $this->fileInfoRepository->updateOrCreate(['attachment_id' => $attachment->id], ['data' => []]);
        }
    }

    public function updateOrCreateFromZip(Attachment $attachment): FileInfo
    {
        try {
            $contentCursor = $this->zipArchiveParser->parseTextContent($attachment);
            $data = [];
            foreach ($contentCursor as $filename => $text) {
                $filename = $this->handleText($filename);
                $data = $this->handleExtractors($filename, $text, $data);
            }

            return $this->fileInfoRepository->updateOrCreate(['attachment_id' => $attachment->id], ['data' => $data]);
        } catch (InvalidEncodingException $e) {
            return $this->fileInfoRepository->updateOrCreate(['attachment_id' => $attachment->id], ['data' => []]);
        }
    }

    private function handleExtractors(string $filename, string $text, array $data): array
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->isTarget($filename)) {
                if ($extractor->isText()) {
                    $text = $this->handleText($text);
                }
                $data[$extractor->getKey()][$filename] = $extractor->extract($text);
            }
        }

        return $data;
    }

    private function handleText(string $text): string
    {
        $text = $this->textService->encoding($text);
        $text = $this->textService->removeBom($text);

        return $text;
    }
}
