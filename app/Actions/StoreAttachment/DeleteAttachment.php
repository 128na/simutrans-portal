<?php

declare(strict_types=1);

namespace App\Actions\StoreAttachment;

use App\Models\Attachment;

class DeleteAttachment
{
    public function __invoke(Attachment $attachment): void
    {
        $attachment->delete();
    }
}
