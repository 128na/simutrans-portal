<?php

declare(strict_types=1);

namespace App\Services\Image;

/**
 * 画像から撮影情報（EXIF・XMP・IPTC・コメント）を取り除くサービス
 *
 * 再エンコードはせず、コンテナのメタデータ部分だけを削るため画質は変わらない。
 * 表示の向きは変えないよう、EXIFのOrientationだけは書き戻す。
 * 画像本体の後ろに付いた追加データ（MPFの副画像・モーションフォトなど）も削る。
 * BMPなど対象外の形式はそのまま返す。
 */
class ImageMetadataStripper
{
    private const PNG_SIGNATURE = "\x89PNG\r\n\x1a\n";

    private const EXIF_HEADER = "Exif\0\0";

    /** 表示に影響するPNGの補助チャンク。ここにない補助チャンクは削る */
    private const PNG_ANCILLARY_CHUNKS = [
        'tRNS', 'gAMA', 'cHRM', 'sRGB', 'iCCP', 'sBIT', 'bKGD', 'pHYs', 'hIST', 'sPLT',
        'cICP', 'mDCv', 'cLLi', 'acTL', 'fcTL', 'fdAT',
    ];

    private const WEBP_CHUNKS = ['VP8 ', 'VP8L', 'VP8X', 'ALPH', 'ANIM', 'ANMF', 'ICCP'];

    /** ループ回数と色の指定に使うGIFのアプリケーション拡張。これ以外は削る */
    private const GIF_APPLICATIONS = ['NETSCAPE2.0', 'ANIMEXTS1.0', 'ICCRGBG1012'];

    /**
     * @throws MetadataStripFailedException 構造を解析できない場合
     */
    public function strip(string $data): string
    {
        return match (true) {
            str_starts_with($data, "\xFF\xD8") => $this->stripJpeg($data),
            str_starts_with($data, self::PNG_SIGNATURE) => $this->stripPng($data),
            str_starts_with($data, 'GIF87a'), str_starts_with($data, 'GIF89a') => $this->stripGif($data),
            str_starts_with($data, 'RIFF') && substr($data, 8, 4) === 'WEBP' => $this->stripWebp($data),
            default => $data,
        };
    }

    private function stripJpeg(string $data): string
    {
        $length = strlen($data);
        $head = '';
        $body = '';
        $orientation = null;
        $hasScan = false;
        $position = 2;

        while ($position < $length) {
            // マーカーまで読み飛ばす（0xFFの詰め物と、紛れ込んだ余分なバイトは捨てる）
            $position = strpos($data, "\xFF", $position);
            if ($position === false) {
                break;
            }

            $position += strspn($data, "\xFF", $position);
            if ($position >= $length) {
                break;
            }

            $marker = ord($data[$position]);
            $position++;

            if ($marker === 0xD9) {
                break;
            }

            // 長さを持たないマーカー
            if ($marker === 0x01 || ($marker >= 0xD0 && $marker <= 0xD8) || $marker === 0x00) {
                continue;
            }

            $segmentLength = $this->readUint($data, $position, 2, false);
            if ($segmentLength < 2 || $position + $segmentLength > $length) {
                throw new MetadataStripFailedException('Invalid JPEG segment length');
            }

            $payload = substr($data, $position + 2, $segmentLength - 2);
            $segment = "\xFF".chr($marker).substr($data, $position, $segmentLength);
            $position += $segmentLength;

            if ($marker === 0xE1 && str_starts_with($payload, self::EXIF_HEADER)) {
                $orientation ??= $this->readOrientation(substr($payload, strlen(self::EXIF_HEADER)));
            }

            if ($marker === 0xE0) {
                // JFIFは埋め込みサムネイルを外して残す。JFXX（サムネイル）などは削る
                if (str_starts_with($payload, "JFIF\0") && strlen($payload) >= 14) {
                    $head .= "\xFF\xE0\x00\x10".substr($payload, 0, 12)."\x00\x00";
                }

                continue;
            }

            if ($this->isJpegMetadata($marker, $payload)) {
                continue;
            }

            $body .= $segment;

            if ($marker === 0xDA) {
                $hasScan = true;
                // 圧縮データは次のマーカーの手前までそのまま写す
                $end = preg_match('/\xFF[^\x00\xD0-\xD7\xFF]/', $data, $matches, PREG_OFFSET_CAPTURE, $position) === 1
                    ? $matches[0][1]
                    : $length;
                $body .= substr($data, $position, $end - $position);
                $position = $end;
            }
        }

        if (! $hasScan) {
            throw new MetadataStripFailedException('JPEG has no image data');
        }

        if ($orientation !== null && $orientation !== 1) {
            $exif = self::EXIF_HEADER.$this->buildOrientationTiff($orientation);
            $head .= "\xFF\xE1".pack('n', strlen($exif) + 2).$exif;
        }

        return "\xFF\xD8".$head.$body."\xFF\xD9";
    }

    /**
     * APP0は呼び出し側で扱う。色の解釈に必要なICCプロファイルとAdobeマーカー以外のAPPnとコメントを削る
     */
    private function isJpegMetadata(int $marker, string $payload): bool
    {
        return match (true) {
            $marker === 0xE2 => ! str_starts_with($payload, "ICC_PROFILE\0"),
            $marker === 0xEE => ! str_starts_with($payload, 'Adobe'),
            $marker >= 0xE1 && $marker <= 0xEF => true,
            default => $marker === 0xFE,
        };
    }

    private function stripPng(string $data): string
    {
        $length = strlen($data);
        $output = self::PNG_SIGNATURE;
        $position = strlen(self::PNG_SIGNATURE);
        $orientation = null;
        $imageDataOffset = null;

        while ($position + 8 <= $length) {
            $chunkLength = $this->readUint($data, $position, 4, false);
            $type = substr($data, $position + 4, 4);
            $size = $chunkLength + 12;

            if ($position + $size > $length) {
                // IENDのCRCが欠けただけのファイルは表示できるので受け付ける
                if ($type === 'IEND') {
                    break;
                }

                throw new MetadataStripFailedException('Invalid PNG chunk length');
            }

            if ($type === 'eXIf') {
                $orientation = $this->readOrientation(substr($data, $position + 8, $chunkLength));
            }

            // 1文字目が大文字のチャンクは表示に必須
            if (ctype_upper($type[0]) || in_array($type, self::PNG_ANCILLARY_CHUNKS, true)) {
                if ($type === 'IDAT') {
                    $imageDataOffset ??= strlen($output);
                }

                $output .= substr($data, $position, $size);
            }

            $position += $size;

            if ($type === 'IEND') {
                break;
            }
        }

        if ($imageDataOffset === null) {
            throw new MetadataStripFailedException('PNG has no image data');
        }

        if ($orientation !== null && $orientation !== 1) {
            $chunk = 'eXIf'.$this->buildOrientationTiff($orientation);
            $chunk = pack('N', strlen($chunk) - 4).$chunk.pack('N', crc32($chunk));
            $output = substr($output, 0, $imageDataOffset).$chunk.substr($output, $imageDataOffset);
        }

        return $output;
    }

    private function stripGif(string $data): string
    {
        $length = strlen($data);
        if ($length < 13) {
            throw new MetadataStripFailedException('Invalid GIF header');
        }

        $position = 13 + $this->gifColorTableSize(ord($data[10]));
        $output = substr($data, 0, $position);

        while ($position < $length) {
            $start = $position;
            $block = ord($data[$position]);

            if ($block === 0x3B) {
                break;
            }

            if ($block === 0x2C) {
                if ($position + 10 > $length) {
                    break;
                }

                // 画像記述子(10) + ローカルカラーテーブル + LZW最小コード長(1)
                $position = $this->skipGifSubBlocks($data, $position + 11 + $this->gifColorTableSize(ord($data[$position + 9])));
                $output .= substr($data, $start, $position - $start);

                continue;
            }

            if ($block !== 0x21 || $position + 2 > $length) {
                // 壊れた末尾は表示に使われないので捨てる
                break;
            }

            $label = ord($data[$position + 1]);
            $application = substr($data, $position + 3, 11);
            $position = $this->skipGifSubBlocks($data, $position + 2);

            $isMetadata = $label === 0xFE
                || ($label === 0xFF && ! in_array($application, self::GIF_APPLICATIONS, true));

            if (! $isMetadata) {
                $output .= substr($data, $start, $position - $start);
            }
        }

        return $output."\x3B";
    }

    private function gifColorTableSize(int $packed): int
    {
        return ($packed & 0x80) !== 0 ? 3 * (2 << ($packed & 0x07)) : 0;
    }

    private function skipGifSubBlocks(string $data, int $position): int
    {
        $length = strlen($data);
        while ($position < $length) {
            $size = ord($data[$position]);
            $position += $size + 1;
            if ($size === 0) {
                break;
            }
        }

        return min($position, $length);
    }

    private function stripWebp(string $data): string
    {
        $end = min(strlen($data), $this->readUint($data, 4, 4, true) + 8);
        $position = 12;
        $chunks = [];
        $orientation = null;

        while ($position + 8 <= $end) {
            $type = substr($data, $position, 4);
            $chunkLength = $this->readUint($data, $position + 4, 4, true);
            if ($position + 8 + $chunkLength > $end) {
                throw new MetadataStripFailedException('Invalid WebP chunk length');
            }

            $payload = substr($data, $position + 8, $chunkLength);
            // 奇数長のチャンクには詰め物が1バイト付く
            $position += 8 + $chunkLength + ($chunkLength % 2);

            if ($type === 'EXIF') {
                $tiff = str_starts_with($payload, self::EXIF_HEADER) ? substr($payload, strlen(self::EXIF_HEADER)) : $payload;
                $orientation = $this->readOrientation($tiff);
            }

            if (in_array($type, self::WEBP_CHUNKS, true)) {
                $chunks[] = [$type, $payload];
            }
        }

        if ($chunks === []) {
            throw new MetadataStripFailedException('WebP has no image data');
        }

        $keepsOrientation = $orientation !== null && $orientation !== 1;
        if ($keepsOrientation) {
            $chunks[] = ['EXIF', $this->buildOrientationTiff((int) $orientation)];
        }

        $output = 'WEBP';
        foreach ($chunks as [$type, $payload]) {
            if ($type === 'VP8X' && $payload !== '') {
                // EXIF(0x08)とXMP(0x04)の有無フラグを実際の内容に合わせる
                $payload[0] = chr((ord($payload[0]) & ~0x0C) | ($keepsOrientation ? 0x08 : 0));
            }

            $output .= $type.pack('V', strlen($payload)).$payload.(strlen($payload) % 2 === 1 ? "\0" : '');
        }

        return 'RIFF'.pack('V', strlen($output)).$output;
    }

    /**
     * EXIFのTIFF構造からOrientation(1〜8)を読む。読めない場合はnull
     */
    private function readOrientation(string $tiff): ?int
    {
        $littleEndian = match (substr($tiff, 0, 2)) {
            'II' => true,
            'MM' => false,
            default => null,
        };
        if ($littleEndian === null || strlen($tiff) < 8) {
            return null;
        }

        $directory = $this->readUint($tiff, 4, 4, $littleEndian);
        if ($directory + 2 > strlen($tiff)) {
            return null;
        }

        $count = $this->readUint($tiff, $directory, 2, $littleEndian);
        for ($i = 0; $i < $count; $i++) {
            $entry = $directory + 2 + $i * 12;
            if ($entry + 12 > strlen($tiff)) {
                break;
            }

            if ($this->readUint($tiff, $entry, 2, $littleEndian) === 0x0112) {
                $value = $this->readUint($tiff, $entry + 8, 2, $littleEndian);

                return $value >= 1 && $value <= 8 ? $value : null;
            }
        }

        return null;
    }

    /**
     * OrientationだけをもつTIFF構造を作る
     */
    private function buildOrientationTiff(int $orientation): string
    {
        return 'MM'.pack('nN', 42, 8)
            .pack('n', 1)
            .pack('nnNnn', 0x0112, 3, 1, $orientation, 0)
            .pack('N', 0);
    }

    private function readUint(string $data, int $offset, int $bytes, bool $littleEndian): int
    {
        $slice = substr($data, $offset, $bytes);
        if (strlen($slice) !== $bytes) {
            throw new MetadataStripFailedException('Unexpected end of image data');
        }

        $value = 0;
        foreach (str_split($littleEndian ? strrev($slice) : $slice) as $byte) {
            $value = ($value << 8) | ord($byte);
        }

        return $value;
    }
}
