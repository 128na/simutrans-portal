<?php

declare(strict_types=1);

namespace App\Enums;

enum ArticleClosureBatchStatus: string
{
    case Pending = 'pending';
    case Sending = 'sending';
    case Sent = 'sent';
    case Failed = 'failed';
    case Unknown = 'unknown';
    case Skipped = 'skipped';
}
