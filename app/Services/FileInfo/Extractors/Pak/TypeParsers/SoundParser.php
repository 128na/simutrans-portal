<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\BinaryReader;
use App\Services\FileInfo\Extractors\Pak\Node;
use App\Services\FileInfo\Extractors\Pak\VersionStamp;

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
class SoundParser implements TypeParserInterface
{
    private const int MAX_SUPPORTED_VERSION = 2;

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
        $stamp = VersionStamp::from($node->data);

        $reader = new BinaryReader($node->data);
        $reader->skip(2);

        return match ($stamp->version) {
            0 => throw InvalidPakFileException::unsupportedTypeVersion('sound', 0, self::MAX_SUPPORTED_VERSION),
            1 => $this->parseVersion1($reader, $stamp->version),
            2 => $this->parseVersion2($reader, $stamp->version),
            default => throw InvalidPakFileException::unsupportedTypeVersion('sound', $stamp->version, self::MAX_SUPPORTED_VERSION),
        };
    }

    /**
     * Version 1: sound_id のみ
     *
     * @return array{version: int, sound_id: int}
     */
    private function parseVersion1(BinaryReader $reader, int $version): array
    {
        return [
            'version' => $version,
            'sound_id' => $reader->readUint16LE(),
        ];
    }

    /**
     * Version 2: sound_id + filename_length + filename
     *
     * @return array{version: int, sound_id: int, filename?: string}
     */
    private function parseVersion2(BinaryReader $reader, int $version): array
    {
        $soundId = $reader->readUint16LE();
        $filenameLength = $reader->readUint16LE();

        $result = [
            'version' => $version,
            'sound_id' => $soundId,
        ];

        // ファイル名が存在する場合
        if ($filenameLength > 0 && $reader->hasMore($filenameLength)) {
            $filename = rtrim($reader->readString($filenameLength), "\0");
            if ($filename !== '') {
                $result['filename'] = $filename;
            }
        }

        return $result;
    }
}
