<?php

declare(strict_types=1);

namespace App\Enums;

enum ArticleClosureRunStatus: string
{
    case Running = 'running';
    case Completed = 'completed';
    case Interrupted = 'interrupted';
}
