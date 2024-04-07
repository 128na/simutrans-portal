<?php

declare(strict_types=1);

namespace App\Console\Commands\Attachment;

use App\Repositories\AttachmentRepository;
use App\Services\Attachment\WebpConverter;
use Illuminate\Console\Command;

final class CommandConvertWeb extends Command
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

    public function handle(): int
    {
        $lazyCollection = $this->attachmentRepository->cursorUnconvertedImages();

        foreach ($lazyCollection as $attachment) {
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
