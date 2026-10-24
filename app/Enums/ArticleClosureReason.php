<?php

declare(strict_types=1);

namespace App\Enums;

enum ArticleClosureReason: string
{
    case DeadLink = 'dead_link';
    case ServiceClosed = 'service_closed';
}
