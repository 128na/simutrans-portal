<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Image;

use App\Services\Image\ImageMetadataStripper;
use App\Services\Image\MetadataStripFailedException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ImageMetadataStripperTest extends TestCase
{
    use BuildsImagesWithMetadata;

    #[DataProvider('jpegEncodings')]
    public function test_jpeg_位置情報などを除き圧縮データは変えない(bool $progressive): void
    {
        $original = $this->jpegWithMetadata(progressive: $progressive);
        $this->assertArrayHasKey('GPSLatitude', $this->readExif($original));

        $stripped = (new ImageMetadataStripper)->strip($original);

        $exif = $this->readExif($stripped);
        $this->assertArrayNotHasKey('GPSLatitude', $exif);
        $this->assertArrayNotHasKey('Model', $exif);
        $this->assertStringNotContainsString('Secret', $stripped);

        // 量子化テーブル以降（画像本体）は1バイトも変わらない
        $plain = $this->plainJpeg($progressive);
        $this->assertSame(
            substr($plain, (int) strpos($plain, "\xFF\xDB")),
            substr($stripped, (int) strpos($stripped, "\xFF\xDB")),
        );
    }

    public static function jpegEncodings(): array
    {
        return ['baseline' => [false], 'progressive' => [true]];
    }

    public function test_jpeg_向きの指定は残す(): void
    {
        $stripped = (new ImageMetadataStripper)->strip($this->jpegWithMetadata(orientation: 6));

        $exif = $this->readExif($stripped);
        $this->assertSame(6, $exif['Orientation']);
        $this->assertArrayNotHasKey('GPSLatitude', $exif);
        $this->assertArrayNotHasKey('Model', $exif);
    }

    public function test_jpeg_色の解釈に必要な情報は残し埋め込みサムネイルは除く(): void
    {
        $jpeg = $this->plainJpeg();
        $icc = $this->jpegSegment(0xE2, "ICC_PROFILE\0\x01\x01KeepProfile");
        $adobe = $this->jpegSegment(0xEE, "Adobe\0\x64\0\0\0\0\x01");
        $segments = $this->jpegSegment(0xE0, "JFIF\0\x01\x02\x01\x00\x48\x00\x48\x01\x01SecretThumb")
            .$this->jpegSegment(0xE0, "JFXX\0\x10SecretThumb")
            .$this->jpegSegment(0xE2, "MPF\0SecretMpf")
            .$icc.$adobe;

        // GDが付けるJFIFを自前のセグメントに差し替える
        $original = substr($jpeg, 0, 2).$segments.substr($jpeg, 4 + (int) unpack('n', substr($jpeg, 4, 2))[1]);

        $stripped = (new ImageMetadataStripper)->strip($original);

        $this->assertStringContainsString($icc, $stripped);
        $this->assertStringContainsString($adobe, $stripped);
        $this->assertStringContainsString("JFIF\0\x01\x02\x01\x00\x48\x00\x48\x00\x00", $stripped);
        $this->assertStringNotContainsString('Secret', $stripped);
        $this->assertNotFalse(imagecreatefromstring($stripped));
    }

    public function test_png_撮影情報を除き画素と透過は変えない(): void
    {
        $stripped = (new ImageMetadataStripper)->strip($this->pngWithMetadata());

        // 追加したチャンクだけが消え、元の画像と一致する
        $this->assertSame($this->plainPng(), $stripped);
    }

    public function test_png_向きの指定は残す(): void
    {
        $stripped = (new ImageMetadataStripper)->strip($this->pngWithMetadata(orientation: 8));

        $this->assertStringContainsString('eXIf', $stripped);
        $this->assertStringNotContainsString('Secret', $stripped);
        $this->assertSame($stripped, (new ImageMetadataStripper)->strip($stripped));
        $this->assertNotFalse(imagecreatefromstring($stripped));
    }

    public function test_gif_アニメーションを保ったままコメントと_xm_pを除く(): void
    {
        $header = 'GIF89a'.pack('vv', 1, 1)."\x80\x00\x00"."\x00\x00\x00\xFF\xFF\xFF";
        $loop = "\x21\xFF\x0BNETSCAPE2.0\x03\x01\x00\x00\x00";
        $frame = "\x21\xF9\x04\x00\x0A\x00\x00\x00"."\x2C".pack('v4', 0, 0, 1, 1)."\x00"."\x02\x02\x44\x01\x00";
        $xmp = "\x21\xFF\x0BXMP DataXMP"."\x0BSecretXmp!!\x00";
        $comment = "\x21\xFE\x0DSecretComment\x00";

        $original = $header.$loop.$xmp.$comment.$frame.$frame."\x3B".'SecretTrailer';

        $stripped = (new ImageMetadataStripper)->strip($original);

        $this->assertSame($header.$loop.$frame.$frame."\x3B", $stripped);
        $this->assertNotFalse(imagecreatefromstring($stripped));
    }

    #[DataProvider('webpOrientations')]
    public function test_webp_撮影情報を除きフラグとサイズを直す(int $orientation, int $expectedFlags): void
    {
        $image = imagecreatetruecolor(4, 4);
        imagefill($image, 0, 0, (int) imagecolorallocate($image, 1, 2, 3));
        ob_start();
        imagewebp($image, null, IMG_WEBP_LOSSLESS);
        $imageChunk = substr((string) ob_get_clean(), 12);

        $chunk = static fn (string $type, string $data): string => $type.pack('V', strlen($data)).$data.(strlen($data) % 2 === 1 ? "\0" : '');
        $header = static fn (int $flags): string => chr($flags)."\0\0\0"."\x03\0\0"."\x03\0\0";
        // 奇数長にして詰め物の扱いも確かめる
        $body = 'WEBP'.$chunk('VP8X', $header(0x0C)).$imageChunk
            .$chunk('EXIF', $this->gpsTiff($orientation).'S')
            .$chunk('XMP ', '<x>SecretXmp</x>!');
        $original = 'RIFF'.pack('V', strlen($body)).$body;

        $stripped = (new ImageMetadataStripper)->strip($original);

        $this->assertStringNotContainsString('Secret', $stripped);
        $this->assertStringNotContainsString('XMP ', $stripped);
        $this->assertStringContainsString($imageChunk, $stripped);
        $this->assertSame($expectedFlags, ord($stripped[20]));
        $this->assertSame(strlen($stripped) - 8, unpack('V', substr($stripped, 4, 4))[1]);
        $this->assertSame($orientation !== 1, str_contains($stripped, 'EXIF'));
        $this->assertNotFalse(imagecreatefromstring($stripped));
    }

    public static function webpOrientations(): array
    {
        return ['向きなし' => [1, 0x00], '向きあり' => [3, 0x08]];
    }

    public function test_対象外の形式はそのまま返す(): void
    {
        $bmp = 'BM'.str_repeat("\x01", 60);

        $this->assertSame($bmp, (new ImageMetadataStripper)->strip($bmp));
    }

    #[DataProvider('brokenImages')]
    public function test_構造を解析できない画像は例外にする(string $data): void
    {
        $this->expectException(MetadataStripFailedException::class);

        (new ImageMetadataStripper)->strip($data);
    }

    public static function brokenImages(): array
    {
        return [
            'jpeg セグメント長超過' => ["\xFF\xD8\xFF\xE1\xFF\xFFExif"],
            'jpeg 画像本体なし' => ["\xFF\xD8\xFF\xD9"],
            'png チャンク長超過' => ["\x89PNG\r\n\x1a\n\x00\x00\xFF\xFFeXIf0000"],
            'gif ヘッダー不足' => ['GIF89a'],
            'webp チャンクなし' => ['RIFF'.pack('V', 4).'WEBP'],
        ];
    }
}
