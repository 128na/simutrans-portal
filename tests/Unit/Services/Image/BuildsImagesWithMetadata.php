<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Image;

/**
 * 撮影情報入りの見本画像を組み立てる
 */
trait BuildsImagesWithMetadata
{
    /** GPS位置情報・カメラ機種・向きを持つJPEG。XMP・IPTC・コメント・末尾の追加データ付き */
    private function jpegWithMetadata(int $orientation = 1, bool $progressive = false): string
    {
        $jpeg = $this->plainJpeg($progressive);

        $segments = $this->jpegSegment(0xE1, "Exif\0\0".$this->gpsTiff($orientation))
            .$this->jpegSegment(0xE1, "http://ns.adobe.com/xap/1.0/\0<x>SecretOwner</x>")
            .$this->jpegSegment(0xED, "Photoshop 3.0\0SecretIptc")
            .$this->jpegSegment(0xFE, 'SecretComment');

        return substr($jpeg, 0, 2).$segments.substr($jpeg, 2).'SecretTrailer';
    }

    private function plainJpeg(bool $progressive = false): string
    {
        $image = imagecreatetruecolor(16, 8);
        imagefilledrectangle($image, 0, 0, 7, 7, (int) imagecolorallocate($image, 200, 30, 40));
        imageinterlace($image, $progressive);

        ob_start();
        imagejpeg($image, null, 90);

        return (string) ob_get_clean();
    }

    private function jpegSegment(int $marker, string $payload): string
    {
        return "\xFF".chr($marker).pack('n', strlen($payload) + 2).$payload;
    }

    /** IFD0（Orientation・Model・GPSへのポインタ）とGPS IFD（北緯）を持つTIFF構造 */
    private function gpsTiff(int $orientation): string
    {
        return 'MM'.pack('nN', 42, 8)
            .pack('n', 3)
            .pack('nnNnn', 0x0112, 3, 1, $orientation, 0)
            .pack('nnNN', 0x0110, 2, 10, 50)
            .pack('nnNN', 0x8825, 4, 1, 60)
            .pack('N', 0)
            ."SecretCam\0"
            .pack('n', 2)
            .pack('nnN', 0x0001, 2, 2)."N\0\0\0"
            .pack('nnNN', 0x0002, 5, 3, 90)
            .pack('N', 0)
            .pack('N6', 35, 1, 40, 1, 12, 1);
    }

    /**
     * @return array<string, mixed>
     */
    private function readExif(string $jpeg): array
    {
        return (array) exif_read_data('data://image/jpeg;base64,'.base64_encode($jpeg));
    }

    /** 透過ピクセルを持つPNGに、撮影情報のチャンクを足したもの */
    private function pngWithMetadata(int $orientation = 1): string
    {
        $png = $this->plainPng();

        $chunks = $this->pngChunk('eXIf', $this->gpsTiff($orientation))
            .$this->pngChunk('tEXt', "Author\0SecretOwner")
            .$this->pngChunk('iTXt', "XML:com.adobe.xmp\0\0\0\0\0<x>SecretXmp</x>")
            .$this->pngChunk('tIME', pack('nC5', 2026, 1, 2, 3, 4, 5));

        // シグネチャ(8) + IHDR(25) の直後に差し込む
        return substr($png, 0, 33).$chunks.substr($png, 33).'SecretTrailer';
    }

    private function plainPng(): string
    {
        $image = imagecreatetruecolor(4, 4);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, (int) imagecolorallocatealpha($image, 10, 20, 30, 127));
        imagesetpixel($image, 1, 1, (int) imagecolorallocatealpha($image, 200, 100, 50, 0));

        ob_start();
        imagepng($image);

        return (string) ob_get_clean();
    }

    private function pngChunk(string $type, string $data): string
    {
        return pack('N', strlen($data)).$type.$data.pack('N', crc32($type.$data));
    }
}
