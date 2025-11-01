<?php

declare(strict_types=1);

namespace App\Repositories\v2;

use App\Models\Attachment;

final class AttachmentRepository
{
    public function __construct(public Attachment $model) {}
}
