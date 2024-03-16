<?php

declare(strict_types=1);

namespace App\Actions\Screenshot;

use App\Models\Attachment;
use App\Models\Screenshot;

class DestroyScreenshot
{
    public function destroy(Screenshot $screenshot): void
    {
        $screenshot->attachments->map(fn (Attachment $attachment) => $attachment->delete());
        $screenshot->delete();
    }
}
