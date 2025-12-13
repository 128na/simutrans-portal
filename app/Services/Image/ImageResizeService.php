<?php

declare(strict_types=1);

namespace App\Services\Image;

use GdImage;

/**
 * 画像を固定幅でリサイズするサービス
 *
 * ResizeByFileSizeを参考に、より汎用的な固定幅リサイズを実装
 */
final class ImageResizeService
{
    /**
     * 画像を指定した幅にリサイズ
     *
     * @param  string  $inputPath  入力画像のパス
     * @param  int  $targetWidth  目標の幅（ピクセル）
     * @param  string  $outputFormat  出力フォーマット（webp, jpeg, png）
     * @return string リサイズ後の画像パス
     *
     * @throws ResizeFailedException
     */
    public function resize(string $inputPath, int $targetWidth, string $outputFormat = 'webp'): string
    {
        $gdImage = $this->getImage($inputPath);
        $originalWidth = @imagesx($gdImage);
        $originalHeight = @imagesy($gdImage);

        // 元画像が目標幅より小さい場合はそのまま返す
        if ($originalWidth <= $targetWidth) {
            return $inputPath;
        }

        // アスペクト比を維持して高さを計算
        $targetHeight = (int) (($targetWidth / $originalWidth) * $originalHeight);

        $resized = @imagescale($gdImage, $targetWidth, $targetHeight, IMG_BILINEAR_FIXED);
        @imagedestroy($gdImage);

        if (! $resized) {
            throw new ResizeFailedException('imagescale failed');
        }

        $outputPath = $this->saveImage($resized, $outputFormat);
        @imagedestroy($resized);

        return $outputPath;
    }

    /**
     * 画像ファイルを読み込んでGdImageオブジェクトを作成
     *
     * @throws ResizeFailedException
     */
    private function getImage(string $path): GdImage
    {
        $data = @file_get_contents($path);
        if ($data === false) {
            throw new ResizeFailedException('file_get_contents failed');
        }

        $image = @imagecreatefromstring($data);
        if ($image instanceof GdImage === false) {
            throw new ResizeFailedException('imagecreatefromstring failed');
        }

        return $image;
    }

    /**
     * GdImageを指定フォーマットでファイルに保存
     *
     * @throws ResizeFailedException
     */
    private function saveImage(GdImage $gdImage, string $format): string
    {
        $tmpPath = @tempnam(sys_get_temp_dir(), 'thumbnail_');
        if ($tmpPath === false) {
            throw new ResizeFailedException('tempnam failed');
        }

        $result = match ($format) {
            'webp' => @imagewebp($gdImage, $tmpPath, 80),
            'jpeg' => @imagejpeg($gdImage, $tmpPath, 80),
            'png' => @imagepng($gdImage, $tmpPath, 6),
            default => throw new ResizeFailedException("Unsupported format: {$format}"),
        };

        if (! $result) {
            @unlink($tmpPath);
            throw new ResizeFailedException("image{$format} failed");
        }

        return $tmpPath;
    }
}
