<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Attachment',
    title: '添付ファイル',
    description: '添付ファイル情報',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1, description: '添付ファイルID'),
        new OA\Property(property: 'attachmentable_type', type: 'string', example: 'Article', description: '関連エンティティタイプ'),
        new OA\Property(property: 'attachmentable_id', type: 'integer', example: 1, description: '関連エンティティID'),
        new OA\Property(property: 'type', type: 'string', example: 'image/png', description: 'ファイルタイプ'),
        new OA\Property(property: 'original_name', type: 'string', example: 'sample.pak', description: 'オリジナルファイル名'),
        new OA\Property(property: 'thumbnail', type: 'string', example: 'https://example.com/thumb.jpg', description: 'サムネイルURL'),
        new OA\Property(property: 'url', type: 'string', example: 'https://example.com/file.pak', description: 'ファイルURL'),
        new OA\Property(property: 'size', type: 'integer', example: 1024, description: 'ファイルサイズ(bytes)'),
        new OA\Property(
            property: 'fileInfo',
            type: 'object',
            nullable: true,
            description: 'ファイル情報',
            properties: [
                new OA\Property(property: 'data', type: 'object', description: 'ファイルメタデータ'),
            ]
        ),
        new OA\Property(property: 'caption', type: 'string', example: 'キャプション', description: '画像キャプション（画像のみ）'),
        new OA\Property(property: 'order', type: 'integer', example: 1, description: '表示順（画像のみ）'),
        new OA\Property(
            property: 'attachmentable',
            type: 'object',
            nullable: true,
            description: '関連記事情報',
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'title', type: 'string'),
            ]
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T12:00:00Z', description: '作成日時'),
    ]
)]
class Attachment {}
