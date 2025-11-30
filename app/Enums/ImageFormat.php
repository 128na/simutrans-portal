<?php

declare(strict_types=1);

namespace App\Enums;

enum ImageFormat: string
{
    case PNG = 'image/png';

    case JPEG = 'image/jpeg';

    case GIF = 'image/gif';

    case WEBP = 'image/webp';

    case BMP = 'image/bmp';
}
