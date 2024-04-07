<?php

declare(strict_types=1);

namespace App\Services\Attachment;

use App\Models\Attachment;
use App\Models\User;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class WebpConverter
{
    private const RESIZE_LIMIT_WIDTH = 3840;

    private const RESIZE_LIMIT_HEIGHT = 2160;

    private const RESIZE_WIDTH = 3840;

    public function create(User $user, UploadedFile $uploadedFile): string
    {
        $gdImage = $this->createFromFile($uploadedFile);
        $resized = $this->resizeIfNeed($gdImage);

        $filepath = $this->getFilepath($user);

        $this->convertToWeb($filepath, $resized);

        return $filepath;
    }

    public function convert(Attachment $attachment): void
    {
        $oldFullpath = $attachment->full_path;
        $image = $this->createFromAttachment($attachment);
        $resized = $this->resizeIfNeed($image);

        /** @var User|null */
        $user = $attachment->user()->withTrashed()->first();

        if (is_null($user)) {
            throw new ConvertFailedException('missing user');
        }

        $filepath = $this->getFilepath($user);

        $this->convertToWeb($filepath, $resized);

        $attachment->update(['path' => $filepath]);

        @unlink($oldFullpath);
    }

    private function getFilepath(User $user): string
    {
        return sprintf('user/%d/%s.webp', $user->id, Str::uuid());
    }

    private function createFromFile(UploadedFile $uploadedFile): GdImage
    {
        $data = $uploadedFile->get();
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

    private function resizeIfNeed(GdImage $gdImage): GdImage
    {
        $width = imagesx($gdImage);
        $height = imagesy($gdImage);
        if ($this->shouldResize($width, $height) === false) {
            return $gdImage;
        }

        $mode = IMG_BILINEAR_FIXED;
        // $mode = IMG_BICUBIC_FIXED;
        // $mode = IMG_MITCHELL; ややぼけた感じになる
        // $mode = IMG_NEAREST_NEIGHBOUR; エッジが立つがサムネ向きではなさそう
        $resized = imagescale($gdImage, self::RESIZE_WIDTH, -1, $mode);
        imagedestroy($gdImage);

        if ($resized instanceof GdImage === false) {
            throw new ConvertFailedException('resdize failed');
        }

        return $resized;
    }

    private function shouldResize(int $width, int $height): bool
    {
        return $width > self::RESIZE_LIMIT_WIDTH || $height > self::RESIZE_LIMIT_HEIGHT;
    }

    private function convertToWeb(string $filepath, GdImage $gdImage): void
    {
        $quality = defined('IMG_WEBP_LOSSLESS') ? IMG_WEBP_LOSSLESS : 100;
        $result = imagewebp($this->convertToTrueColor($gdImage), $this->getSavepath($filepath), $quality);
        imagedestroy($gdImage);

        if ($result === false) {
            throw new ConvertFailedException('webp failed');
        }
    }

    private function convertToTrueColor(GdImage $gdImage): GdImage
    {
        $converted = imagecreatetruecolor(imagesx($gdImage), imagesy($gdImage));
        if ($converted === false) {
            throw new ConvertFailedException('imagecreatetruecolor failed');
        }

        $result = imagecopy($converted, $gdImage, 0, 0, 0, 0, imagesx($gdImage), imagesy($gdImage));

        if ($result === false) {
            throw new ConvertFailedException('imagecopy failed');
        }

        imagedestroy($gdImage);

        return $converted;
    }

    private function getSavepath(string $filepath): string
    {
        return storage_path(sprintf('app/public/%s', $filepath));
    }
}
