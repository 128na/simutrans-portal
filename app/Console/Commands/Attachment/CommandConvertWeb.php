<?php

namespace App\Console\Commands\Attachment;

use App\Repositories\AttachmentRepository;
use App\Services\Attachment\WebpConverter;
use Illuminate\Console\Command;

class CommandConvertWeb extends Command
{
    /**
     * @var string
     */
    protected $signature = 'convert:webp';

    /**
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
        private readonly WebpConverter $webpConverter,
        private readonly AttachmentRepository $attachmentRepository,
    ) {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function handle()
    {
        $cursor = $this->attachmentRepository->cursorUnconvertedImages();

        foreach ($cursor as $attachment) {
            try {
                $this->info(sprintf('convert: %s', $attachment->path));
                $this->webpConverter->convert($attachment);
            } catch (\Throwable $th) {
                $this->error($th->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
