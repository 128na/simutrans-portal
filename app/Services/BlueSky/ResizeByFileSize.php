<?php

declare(strict_types=1);

namespace App\Services\BlueSky;

use GdImage;

final class ResizeByFileSize
{
    public function __invoke(string $inputPath, int $targetFileSize): string
    {
        $filesize = filesize($inputPath);
        if ($filesize === false || $filesize < $targetFileSize) {
            return $inputPath;
        }

        $gdImage = $this->getImage($inputPath);
        $originalWidth = imagesx($gdImage);
        if ($originalWidth === false) {
            throw new ResizeFailedException('imagesx failed');
        }
        logger('[FileSizeBaseResizer] resize', ['originalWidth' => $originalWidth, 'filesize' => $filesize]);

        return $this->findResized($gdImage, $originalWidth, $targetFileSize);
    }

    private function findResized(GdImage $gdImage, int $originalWidth, int $targetFileSize): string
    {
        $width = (int) ($originalWidth / 2);
        $min = (int) ($targetFileSize * 0.75);
        $max = $targetFileSize;
        $attempt = 0;
        $limit = 20;

        do {
            $resized = $this->doResize($gdImage, $width);
            $size = filesize($resized);
            if ($size === false || $size === 0) {
                throw new ResizeFailedException('filesize failed');
            }

            logger('[FileSizeBaseResizer] resize', ['attempt' => $attempt, 'width' => $width, 'size' => $size]);
            if ($min < $size && $size < $max) {
                return $resized;
            }

            $width = (int) ($width / 2);
            if ($size <= $min) {
                $width = (int) (($originalWidth + $width) / 2);
            }

            if (is_file($resized)) {
                unlink($resized);
            }
            $attempt++;
        } while ($attempt <= $limit);

        throw new ResizeFailedException('attempt limit reached');
    }

    private function getImage(string $path): GdImage
    {
        $data = file_get_contents($path);
        if ($data === false) {
            throw new ResizeFailedException('file_get_contents failed');
        }

        $image = imagecreatefromstring($data);
        if ($image instanceof GdImage === false) {
            throw new ResizeFailedException('getImage failed');
        }

        return $image;
    }

    private function doResize(GdImage $gdImage, int $width): string
    {
        $resized = imagescale($gdImage, $width, -1, IMG_BILINEAR_FIXED);
        if ($resized === false) {
            throw new ResizeFailedException('imagescale failed');
        }

        $tmpPath = tempnam(sys_get_temp_dir(), '');
        if ($tmpPath === false) {
            throw new ResizeFailedException('tempnam failed');
        }
        $result = imagewebp($resized, $tmpPath);
        imagedestroy($resized);
        if ($result === false) {
            throw new ResizeFailedException('imagewebp failed');
        }

        return $tmpPath;
    }
}
