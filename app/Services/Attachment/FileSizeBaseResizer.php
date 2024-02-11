<?php

declare(strict_types=1);

namespace App\Services\Attachment;

use App\Services\Service;
use GdImage;

class FileSizeBaseResizer extends Service
{
    public function resize(string $inputPath, int $targetFileSize): string
    {
        $filesize = @filesize($inputPath);
        if ($filesize < $targetFileSize) {
            return $inputPath;
        }

        $im = $this->getImage($inputPath);
        $originalWidth = @imagesx($im);
        logger('FileSizeBaseResizer::resize', compact('originalWidth'));
        if (!$originalWidth) {
            throw new ConvertFailedException('imagesx failed');
        }
        $width = intval($originalWidth / 2);
        $min = $targetFileSize * 0.9;
        $max = $targetFileSize;
        $attempt = 0;
        $limit = 10;
        do {
            $resized = $this->doResize($im, $width);
            $size = @filesize($resized);
            if (!$size) {
                throw new ConvertFailedException('filesize failed');
            }
            logger('FileSizeBaseResizer::resize', compact('attempt', 'size'));
            if ($min < $size && $size < $max) {
                return $resized;
            }
            if ($size <= $min) {
                $width = intval(($originalWidth + $width) / 2);
            }
            if ($size >= $max) {
                $width = intval($width / 2);
            }
            @unlink($resized);
            $attempt++;
        } while ($attempt <= $limit);
        throw new ConvertFailedException('attempt limit reached');
    }

    private function getImage(string $path): GdImage
    {
        $data = @file_get_contents($path);
        if ($data === false) {
            throw new ConvertFailedException('file_get_contents failed');
        }

        $image = imagecreatefromstring($data);
        if ($image instanceof GdImage === false) {
            throw new ConvertFailedException('getImage failed');
        }

        return $image;
    }

    private function doResize(GdImage $im, int $width): string
    {
        $resized = @imagescale($im, $width, -1, IMG_BILINEAR_FIXED);
        if (!$resized) {
            throw new ConvertFailedException('imagescale failed');
        }
        $tmpPath = @tempnam(sys_get_temp_dir(), '');
        if (!$tmpPath) {
            throw new ConvertFailedException('tempnam failed');
        }
        $result = @imagewebp($resized, $tmpPath);
        @imagedestroy($resized);
        if (!$result) {
            throw new ConvertFailedException('imagewebp failed');
        }

        return $tmpPath;
    }
}
