<?php

declare(strict_types=1);

namespace App\Actions\StoreAttachment;

use App\Models\Attachment;
use App\Repositories\AttachmentRepository;

class DeleteAttachment
{
    public function __construct(
        private readonly AttachmentRepository $attachmentRepository,
    ) {}

    public function __invoke(Attachment $attachment): void
    {
        $this->attachmentRepository->delete($attachment);
    }
}
