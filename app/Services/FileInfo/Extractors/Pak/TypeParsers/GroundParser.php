<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;

/**
 * Ground（地形テクスチャ）パーサー
 *
 * - obj_ground（地形テクスチャ）
 * - 地面のテクスチャ（grass、dirt、snow など）を定義
 * - データフィールドなし（画像のみ）
 *
 * 特記事項:
 * - ground_reader.cc では read_node() が obj_reader_t::read_node<ground_desc_t>(info) を呼び出すのみ
 * - データフィールドがないため、基本情報のみ返す
 * - 画像は子ノード（image-array）に格納
 *
 * @see simutrans/descriptor/reader/ground_reader.cc
 */
class GroundParser implements TypeParserInterface
{
    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_GROUND;
    }

    /**
     * @return array{
     *     version: int,
     *     has_data: bool
     * }
     */
    public function parse(Node $node): array
    {
        // ground オブジェクトはデータフィールドを持たない
        // 画像のみが子ノード（image-array）に含まれる
        return [
            'version' => 0,
            'has_data' => false,
        ];
    }
}
