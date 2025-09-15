<?php

declare(strict_types=1);

namespace App\Actions\StoreAttachment;

use App\Enums\CroppableFormat;

final class CropImage
{
    public function __invoke(string $fullpath, int $top = 0, int $bottom = 0, int $left = 0, int $right = 0): void
    {
        $mime = $this->getMime($fullpath);
        if (!($mime instanceof \App\Enums\CroppableFormat)) {
            throw new ConvertFailedException('unsupport format:' . $fullpath);
        }

        $im = match ($mime) {
            CroppableFormat::PNG => imagecreatefrompng($fullpath),
            CroppableFormat::JPEG => imagecreatefromjpeg($fullpath),
            CroppableFormat::GIF => imagecreatefromgif($fullpath),
            CroppableFormat::WEBP => imagecreatefromwebp($fullpath),
            CroppableFormat::BMP => imagecreatefrombmp($fullpath),
        };

        if (!$im) {
            throw new ConvertFailedException('imagecreatefromgd failed:' . $fullpath);
        }

        $cropped = imagecrop($im, [
            'x' => $left,
            'y' => $top,
            'width' => (imagesx($im) - $left) - $right,
            'height' => (imagesy($im) - $top) - $bottom,
        ]);
        imagedestroy($im);
        if (!$cropped) {
            throw new ConvertFailedException('imagecrop failed:' . $fullpath);
        }

        $result = match ($mime) {
            CroppableFormat::PNG => imagepng($cropped, $fullpath, 9),
            CroppableFormat::BMP => imagepng($cropped, $fullpath, 9),
            CroppableFormat::JPEG => imagejpeg($cropped, $fullpath, 100),
            CroppableFormat::GIF => imagegif($cropped, $fullpath),
            CroppableFormat::WEBP => imagewebp($cropped, $fullpath, IMG_WEBP_LOSSLESS),
        };

        imagedestroy($cropped);
        if (!$result) {
            throw new ConvertFailedException('imagepng failed:' . $fullpath);
        }
    }

    private function getMime(string $fullpath): null|CroppableFormat
    {
        return CroppableFormat::tryFrom(mime_content_type($fullpath) ?: '');
    }
}
