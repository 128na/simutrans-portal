<?php

declare(strict_types=1);

namespace App\Services\Attachment;

use App\Services\Service;

class ImageCropper extends Service
{
    public function crop(string $fullpath, int $top = 0, int $bottom = 0, int $left = 0, int $right = 0): void
    {
        $type = mime_content_type($fullpath);
        if (! $type) {
            throw new ConvertFailedException('mime_content_type failed:'.$fullpath);
        }
        $im = match ($type) {
            'image/png' => imagecreatefrompng($fullpath),
            'image/jpeg' => imagecreatefromjpeg($fullpath),
            'image/gif' => imagecreatefromgif($fullpath),
            'image/webp' => imagecreatefromwebp($fullpath),
            'image/bmp' => imagecreatefrombmp($fullpath),
            default => throw new ConvertFailedException('unsupport format:'.$fullpath),
        };

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

        $result = match ($type) {
            'image/png' => imagepng($cropped, $fullpath, 9),
            'image/bmp' => imagepng($cropped, $fullpath, 9),
            'image/jpeg' => imagejpeg($cropped, $fullpath, 100),
            'image/gif' => imagegif($cropped, $fullpath),
            'image/webp' => imagewebp($cropped, $fullpath, IMG_WEBP_LOSSLESS),
        };

        imagedestroy($cropped);
        if (! $result) {
            throw new ConvertFailedException('imagepng failed:'.$fullpath);
        }
    }
}
