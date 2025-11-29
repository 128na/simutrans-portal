<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;
use RuntimeException;

/**
 * Pedestrian（歩行者）パーサー
 *
 * - obj_pedestrian（歩行者）
 * - 街中を歩く歩行者の外観と動作を定義
 * - バージョン 0〜2 に対応
 *
 * バージョン履歴:
 * - v0: 旧形式。distribution_weight のみ（firstUint16 が weight として使用される）
 * - v1: steps_per_frame（歩行速度）、offset（描画オフセット）を追加
 * - v2: intro_date（導入日）、retire_date（廃止日）を追加
 *
 * @see simutrans/descriptor/reader/pedestrian_reader.cc
 */
final readonly class PedestrianParser implements TypeParserInterface
{
    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_PEDESTRIAN;
    }

    /**
     * @return array{
     *     version: int,
     *     distribution_weight: int,
     *     steps_per_frame?: int,
     *     offset?: int,
     *     intro_date?: int,
     *     retire_date?: int
     * }
     */
    public function parse(Node $node): array
    {
        $firstUint16 = (unpack('v', substr($node->data, 0, 2)) ?: [])[1] ?? 0;
        $version = (($firstUint16 & 0x8000) !== 0) ? ($firstUint16 & 0x7FFF) : 0;

        return match ($version) {
            0 => $this->parseVersion0($firstUint16),
            1 => $this->parseVersion1($node->data),
            2 => $this->parseVersion2($node->data),
            default => throw new RuntimeException('Unsupported pedestrian version: ' . $version),
        };
    }

    /**
     * Version 0: 旧形式。firstUint16 が distribution_weight
     *
     * @return array{version: int, distribution_weight: int}
     */
    private function parseVersion0(int $firstUint16): array
    {
        return [
            'version' => 0,
            'distribution_weight' => $firstUint16,
        ];
    }

    /**
     * Version 1: steps_per_frame、offset を追加
     *
     * @return array{
     *     version: int,
     *     distribution_weight: int,
     *     steps_per_frame: int,
     *     offset: int
     * }
     */
    private function parseVersion1(string $data): array
    {
        $unpacked = unpack(
            'vversion/vdistribution_weight/vsteps_per_frame/voffset',
            substr($data, 0, 8)
        ) ?: [];

        return [
            'version' => $unpacked['version'] & 0x7FFF,
            'distribution_weight' => $unpacked['distribution_weight'],
            'steps_per_frame' => $unpacked['steps_per_frame'],
            'offset' => $unpacked['offset'],
        ];
    }

    /**
     * Version 2: intro_date、retire_date を追加
     *
     * @return array{
     *     version: int,
     *     distribution_weight: int,
     *     steps_per_frame: int,
     *     offset: int,
     *     intro_date: int,
     *     retire_date: int
     * }
     */
    private function parseVersion2(string $data): array
    {
        $unpacked = unpack(
            'vversion/vdistribution_weight/vsteps_per_frame/voffset/vintro_date/vretire_date',
            substr($data, 0, 12)
        ) ?: [];

        return [
            'version' => $unpacked['version'] & 0x7FFF,
            'distribution_weight' => $unpacked['distribution_weight'],
            'steps_per_frame' => $unpacked['steps_per_frame'],
            'offset' => $unpacked['offset'],
            'intro_date' => $unpacked['intro_date'],
            'retire_date' => $unpacked['retire_date'],
        ];
    }
}
