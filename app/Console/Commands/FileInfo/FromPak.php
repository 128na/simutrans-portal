<?php

namespace App\Console\Commands\FileInfo;

use App\Repositories\Attachment\FileInfoRepository;
use App\Repositories\AttachmentRepository;
use App\Services\FileInfo\Extractors\Extractor;
use App\Services\FileInfo\Extractors\PakExtractor;
use App\Services\FileInfo\InvalidEncodingException;
use Illuminate\Console\Command;
use Throwable;

class FromPak extends Command
{
    protected $signature = 'fileinfo:pak';

    protected $description = 'Update pak file info';

    /**
     * @param Extractor[] $extractors
     */
    public function __construct(
        private AttachmentRepository $attachmentRepository,
        private FileInfoRepository $fileInfoRepository,
        private PakExtractor $pakExtractor,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $cursor = $this->attachmentRepository->cursorPakFileAttachment();

        foreach ($cursor as $attachment) {
            try {
                $filename = $attachment->original_name;
                $text = file_get_contents($attachment->full_path);
                $data = [
                    $this->pakExtractor->getKey() => [
                        $filename => $this->pakExtractor->extract($text),
                    ],
                ];

                $this->fileInfoRepository->updateOrCreate(['attachment_id' => $attachment->id], ['data' => $data]);
            } catch (InvalidEncodingException $e) {
                $this->fileInfoRepository->updateOrCreate(['attachment_id' => $attachment->id], ['data' => []]);
            } catch (Throwable $e) {
                report($e);
                $this->error($e->getMessage());
            }
        }

        return 0;
    }
}
