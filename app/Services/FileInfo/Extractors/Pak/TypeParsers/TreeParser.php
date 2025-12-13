<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Enums\SimutransClimate;
use App\Services\FileInfo\Extractors\Pak\Node;
use RuntimeException;

/**
 * Tree（木）パーサー
 *
 * - obj_tree（木）
 * - 景観の木を定義
 * - バージョン 0〜2 に対応
 *
 * バージョン履歴:
 * - v0: 旧形式。バージョン情報なし、デフォルト値を使用
 * - v1: 旧形式。allowed_climates 未対応（北極除外）、hoehenlage は無視
 * - v2: allowed_climates（気候ビットマスク）、distribution_weight、number_of_seasons を追加
 *
 * 気候ビットマスク (climate_bits):
 * - 1 << 0 = water_climate
 * - 1 << 1 = desert_climate
 * - 1 << 2 = tropic_climate
 * - 1 << 3 = mediterran_climate
 * - 1 << 4 = temperate_climate
 * - 1 << 5 = tundra_climate
 * - 1 << 6 = rocky_climate
 * - 1 << 7 = arctic_climate
 *
 * @see simutrans/descriptor/reader/tree_reader.cc
 */
class TreeParser implements TypeParserInterface
{
    private const int DEFAULT_DISTRIBUTION_WEIGHT = 3;

    private const int ALL_BUT_ARCTIC_CLIMATE = 0x7F; // 北極以外すべて (0b01111111)

    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_TREE;
    }

    /**
     * @return array{
     *     version: int,
     *     allowed_climates: int,
     *     distribution_weight: int,
     *     number_of_seasons: int
     * }
     */
    public function parse(Node $node): array
    {
        $firstUint16 = (unpack('v', substr($node->data, 0, 2)) ?: [])[1] ?? 0;
        $version = (($firstUint16 & 0x8000) !== 0) ? ($firstUint16 & 0x7FFF) : 0;

        $result = match ($version) {
            0 => $this->parseVersion0(),
            1 => $this->parseVersion1($node->data),
            2 => $this->parseVersion2($node->data),
            default => throw new RuntimeException('Unsupported tree version: '.$version),
        };

        return $this->buildResult($result);
    }

    /**
     * Version 0: 旧形式。デフォルト値のみ
     *
     * @return array{
     *     version: int,
     *     allowed_climates: int,
     *     distribution_weight: int,
     *     number_of_seasons: int
     * }
     */
    private function parseVersion0(): array
    {
        return [
            'version' => 0,
            'allowed_climates' => self::ALL_BUT_ARCTIC_CLIMATE,
            'distribution_weight' => self::DEFAULT_DISTRIBUTION_WEIGHT,
            'number_of_seasons' => 0,
        ];
    }

    /**
     * Version 1: 旧バージョン。hoehenlage は無視、distribution_weight のみ読み取り
     *
     * @return array{
     *     version: int,
     *     allowed_climates: int,
     *     distribution_weight: int,
     *     number_of_seasons: int
     * }
     */
    private function parseVersion1(string $data): array
    {
        $unpacked = unpack(
            'vversion/Choehenlage/Cdistribution_weight',
            substr($data, 0, 4)
        ) ?: [];

        return [
            'version' => $unpacked['version'] & 0x7FFF,
            'allowed_climates' => self::ALL_BUT_ARCTIC_CLIMATE,
            'distribution_weight' => $unpacked['distribution_weight'],
            'number_of_seasons' => 0,
        ];
    }

    /**
     * Version 2: 最新版。すべてのフィールドを読み取り
     *
     * @return array{
     *     version: int,
     *     allowed_climates: int,
     *     distribution_weight: int,
     *     number_of_seasons: int
     * }
     */
    private function parseVersion2(string $data): array
    {
        $unpacked = unpack(
            'vversion/vallowed_climates/Cdistribution_weight/Cnumber_of_seasons',
            substr($data, 0, 6)
        ) ?: [];

        return [
            'version' => $unpacked['version'] & 0x7FFF,
            'allowed_climates' => $unpacked['allowed_climates'],
            'distribution_weight' => $unpacked['distribution_weight'],
            'number_of_seasons' => $unpacked['number_of_seasons'],
        ];
    }

    /**
     * 気候名の文字列表現を追加
     *
     * @param  array{
     *     version: int,
     *     allowed_climates: int,
     *     distribution_weight: int,
     *     number_of_seasons: int
     * }  $data
     * @return array{
     *     version: int,
     *     allowed_climates: int,
     *     distribution_weight: int,
     *     number_of_seasons: int
     * }
     */
    private function buildResult(array $data): array
    {
        $climateNames = SimutransClimate::fromBitFlags($data['allowed_climates']);
        $data['climate_names'] = $climateNames;

        return $data;
    }
}
