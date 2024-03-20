<?php

declare(strict_types=1);

namespace App\Services\Attachment;

use App\Services\Service;

class ImageCropper extends Service
{
    public function crop(string $fullpath, int $top = 0, int $bottom = 0, int $left = 0, int $right = 0): void
    {
        $im = imagecreatefrompng($fullpath);
        if (! $im) {
            throw new ConvertFailedException('imagecreatefromgd failed:'.$fullpath);
        }
        $cropped = imagecrop($im, [
            'x' => $left,
            'y' => $top,
            'width' => imagesx($im) - $left - $right,
            'height' => imagesy($im) - $top - $bottom,
        ]);
        imagedestroy($im);
        if (! $cropped) {
            throw new ConvertFailedException('imagecrop failed:'.$fullpath);
        }

        $result = imagepng($cropped, $fullpath);
        imagedestroy($cropped);
        if (! $result) {
            throw new ConvertFailedException('imagepng failed:'.$fullpath);
        }
    }
}
