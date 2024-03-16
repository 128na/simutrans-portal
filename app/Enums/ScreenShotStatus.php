<?php

declare(strict_types=1);

namespace App\Enums;

enum ScreenShotStatus: string
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
