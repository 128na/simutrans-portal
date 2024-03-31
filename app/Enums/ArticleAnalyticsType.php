<?php

declare(strict_types=1);

namespace App\Enums;

enum ArticleAnalyticsType: int
{
    /**
     * 日次
     */
    case Daily = 1;

    /**
     * 月次
     */
    case Monthly = 2;

    /**
     * 年次
     */
    case Yearly = 3;
}
