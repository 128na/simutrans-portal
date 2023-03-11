<?php

declare(strict_types=1);

namespace App\Services\Attachment;

use App\Models\Attachment;
use App\Models\User;
use App\Services\Service;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

class WebpConverter extends Service
{
    private const RESIZE_LIMIT_WIDTH = 1280;

    private const RESIZE_LIMIT_HEIGHT = 960;

    private const RESIZE_WIDTH = 1280;

    private const WEBP_QUALITY = 100;

    public function create(User $user, File $file): string
    {
        $image = $this->createFromFile($file);
        $resized = $this->resizeIfNeed($image);

        $filepath = $this->getFilepath($user);

        $this->convertToWeb($filepath, $resized);

        return $filepath;
    }

    public function convert(Attachment $attachment): void
    {
        $oldFullpath = $attachment->full_path;
        $image = $this->createFromAttachment($attachment);
        $resized = $this->resizeIfNeed($image);

        $filepath = $this->getFilepath($attachment->user()->withTrashed()->first());

        $this->convertToWeb($filepath, $resized);

        $attachment->update(['path' => $filepath]);

        @unlink($oldFullpath);
    }

    private function getFilepath(User $user): string
    {
        return sprintf('user/%d/%s.webp', $user->id, Str::uuid());
    }

    private function createFromFile(UploadedFile $file): GdImage
    {
        $data = $file->get();
        if ($data === false) {
            throw new ConvertFailedException('get file data failed');
        }

        $image = imagecreatefromstring($data);
        if ($image instanceof GdImage === false) {
            throw new ConvertFailedException('create failed');
        }

        return $image;
    }

    private function createFromAttachment(Attachment $attachment): GdImage
    {
        $data = @file_get_contents($attachment->full_path);
        if ($data === false) {
            throw new ConvertFailedException('get file data failed');
        }

        $image = imagecreatefromstring($data);
        if ($image instanceof GdImage === false) {
            throw new ConvertFailedException('create failed');
        }

        return $image;
    }

    private function resizeIfNeed(GdImage $image): GdImage
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
            throw new ConvertFailedException('resdize failed');
        }

        return $resized;
    }

    private function shouldResize(int $width, int $height): bool
    {
        return $width > self::RESIZE_LIMIT_WIDTH || $height > self::RESIZE_LIMIT_HEIGHT;
    }

    private function convertToWeb(string $filepath, GdImage $image): void
    {
        $result = imagewebp($this->convertToTrueColor($image), $this->getSavepath($filepath), self::WEBP_QUALITY);
        imagedestroy($image);

        if ($result === false) {
            throw new ConvertFailedException('webp failed');
        }
    }

    private function convertToTrueColor(GdImage $image): GdImage
    {
        $converted = imagecreatetruecolor(imagesx($image), imagesy($image));
        if ($converted === false) {
            throw new ConvertFailedException('imagecreatetruecolor failed');
        }
        $result = imagecopy($converted, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));

        if ($result === false) {
            throw new ConvertFailedException('imagecopy failed');
        }
        imagedestroy($image);

        return $converted;
    }

    private function getSavepath(string $filepath): string
    {
        return storage_path(sprintf('app/public/%s', $filepath));
    }
}
