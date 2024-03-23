<?php

declare(strict_types=1);

namespace App\Enums;

enum ScreenshotStatus: string
{
    /**
     * 公開
     */
    case Publish = 'Publish';

    /**
     * 非公開
     */
    case Private = 'Private';
}
