<?php

declare(strict_types=1);

namespace App\Services\FileInfo\Extractors\Pak\TypeParsers;

use App\Services\FileInfo\Extractors\Pak\Node;

/**
 * Skin（スキン/UI画像）パーサー
 *
 * - obj_skin（スキン）
 * - UI要素の画像を定義
 * - データフィールドなし（画像のみ）
 *
 * 特記事項:
 * - skin_reader.cc では read_node() が obj_reader_t::read_node<skin_desc_t>(info) を呼び出すのみ
 * - データフィールドがないため、基本情報のみ返す
 * - 画像は子ノード（image-list）に格納
 * - smoke（煙）もこのタイプを使用（特殊ケース）
 *
 * @see simutrans/descriptor/reader/skin_reader.cc
 */
final readonly class SkinParser implements TypeParserInterface
{
    public function canParse(Node $node): bool
    {
        return $node->type === Node::OBJ_SKIN || $node->type === 'smoke';
    }

    /**
     * @return array{
     *     version: int,
     *     has_data: bool,
     *     object_subtype: string
     * }
     */
    public function parse(Node $node): array
    {
        // skin/smoke オブジェクトはデータフィールドを持たない
        // 画像のみが子ノード（image-list）に含まれる
        return [
            'version' => 0,
            'has_data' => false,
            'object_subtype' => $node->type, // 'skin' または 'smoke'
        ];
    }
}
