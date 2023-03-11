<?php

declare(strict_types=1);

namespace App\Services\Attachment;

use App\Models\User;
use App\Services\Service;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class WebpConverter extends Service
{
    private const RESIZE_LIMIT_WIDTH = 1280;

    private const RESIZE_LIMIT_HEIGHT = 960;

    private const RESIZE_WIDTH = 1280;

    private const WEBP_QUALITY = 100;

    public function create(User $user, UploadedFile $file): string
    {
        $image = $this->createFromFile($file);
        $resized = $this->resizeIfNeed($image, $file);

        return $this->convertToWeb($user, $resized, $file);
    }

    private function createFromFile(UploadedFile $file): GdImage
    {
        $data = $file->get();
        if ($data === false) {
            throw new ConvertFailedException('get file data failed: '.$file->getClientOriginalName());
        }

        $image = imagecreatefromstring($data);
        if ($image instanceof GdImage === false) {
            throw new ConvertFailedException('create failed: '.$file->getClientOriginalName());
        }

        return $image;
    }

    private function resizeIfNeed(GdImage $image, UploadedFile $file): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        if ($this->shouldResize($width, $height) === false) {
            return $image;
        }

        $mode = IMG_BILINEAR_FIXED;
        // $mode = IMG_BICUBIC_FIXED;
        // $mode = IMG_MITCHELL; ややぼけた感じになる
        // $mode = IMG_NEAREST_NEIGHBOUR; エッジが立つがサムネ向きではなさそう
        $resized = imagescale($image, self::RESIZE_WIDTH, -1, $mode);
        imagedestroy($image);

        if ($resized instanceof GdImage === false) {
            throw new ConvertFailedException('resdize failed: '.$file->getClientOriginalName());
        }

        return $resized;
    }

    private function shouldResize(int $width, int $height): bool
    {
        return $width > self::RESIZE_LIMIT_WIDTH || $height > self::RESIZE_LIMIT_HEIGHT;
    }

    private function convertToWeb(User $user, GdImage $image, UploadedFile $file): string
    {
        $filepath = $this->getFilepath($user);
        $result = imagewebp($image, $this->getSavepath($filepath), self::WEBP_QUALITY);
        imagedestroy($image);

        if ($result === false) {
            throw new ConvertFailedException('webp failed: '.$file->getClientOriginalName());
        }

        return $filepath;
    }

    private function getFilepath(User $user): string
    {
        return sprintf('user/%d/%s.webp', $user->id, Str::uuid());
    }

    private function getSavepath(string $filepath): string
    {
        return storage_path(sprintf('app/public/%s', $filepath));
    }
}
