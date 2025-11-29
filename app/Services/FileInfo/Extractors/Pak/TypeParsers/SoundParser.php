<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;
use RuntimeException;

/**
 * Sound（効果音）パーサー
 *
 * - obj_sound（効果音）
 * - ゲーム内効果音を定義
 * - バージョン 1〜2 に対応
 *
 * バージョン履歴:
 * - v0: 存在しない（エラー）
 * - v1: nr（サウンドID）のみ
 * - v2: nr + filename_length + filename（ファイル名文字列）
 *
 * 特記事項:
 * - サウンドファイル名は子ノード（name）に格納されるが、v2ではデータ内にも含まれる
 * - システム効果音は固定ID（0-15）を使用
 *
 * @see simutrans/descriptor/reader/sound_reader.cc
 */
final readonly class SoundParser implements TypeParserInterface
{
    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_SOUND;
    }

    /**
     * @return array{
     *     version: int,
     *     sound_id: int,
     *     filename?: string
     * }
     */
    public function parse(Node $node): array
    {
        $firstUint16 = (unpack('v', substr($node->data, 0, 2)) ?: [])[1] ?? 0;
        $version = (($firstUint16 & 0x8000) !== 0) ? ($firstUint16 & 0x7FFF) : 0;

        return match ($version) {
            0 => throw new RuntimeException('Sound version 0 does not exist'),
            1 => $this->parseVersion1($node->data),
            2 => $this->parseVersion2($node->data),
            default => throw new RuntimeException('Unsupported sound version: ' . $version),
        };
    }

    /**
     * Version 1: sound_id のみ
     *
     * @return array{version: int, sound_id: int}
     */
    private function parseVersion1(string $data): array
    {
        $unpacked = unpack(
            'vversion/vsound_id',
            substr($data, 0, 4)
        ) ?: [];

        return [
            'version' => ($unpacked['version'] ?? 0) & 0x7FFF,
            'sound_id' => $unpacked['sound_id'] ?? 0,
        ];
    }

    /**
     * Version 2: sound_id + filename_length + filename
     *
     * @return array{version: int, sound_id: int, filename?: string}
     */
    private function parseVersion2(string $data): array
    {
        $unpacked = unpack(
            'vversion/vsound_id/vfilename_length',
            substr($data, 0, 6)
        ) ?: [];

        $version = ($unpacked['version'] ?? 0) & 0x7FFF;
        $soundId = $unpacked['sound_id'] ?? 0;
        $filenameLength = $unpacked['filename_length'] ?? 0;

        $result = [
            'version' => $version,
            'sound_id' => $soundId,
        ];

        // ファイル名が存在する場合
        if ($filenameLength > 0 && strlen($data) >= 6 + $filenameLength) {
            $filename = substr($data, 6, $filenameLength);
            // null終端を削除
            $filename = rtrim($filename, "\0");
            if ($filename !== '') {
                $result['filename'] = $filename;
            }
        }

        return $result;
    }
}
