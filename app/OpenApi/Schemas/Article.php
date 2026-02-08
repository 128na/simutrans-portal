<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Article',
    title: '記事',
    description: '記事情報',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1, description: '記事ID'),
        new OA\Property(property: 'title', type: 'string', example: '新しいアドオン', description: 'タイトル'),
        new OA\Property(property: 'slug', type: 'string', example: 'new-addon', description: 'スラッグ'),
        new OA\Property(property: 'status', type: 'string', example: 'publish', description: 'ステータス', enum: ['publish', 'draft', 'private']),
        new OA\Property(property: 'post_type', type: 'string', example: 'addon-post', description: '投稿タイプ'),
        new OA\Property(property: 'contents', type: 'object', description: 'コンテンツデータ'),
        new OA\Property(property: 'categories', type: 'array', description: 'カテゴリID配列', items: new OA\Items(type: 'integer')),
        new OA\Property(property: 'tags', type: 'array', description: 'タグID配列', items: new OA\Items(type: 'integer')),
        new OA\Property(property: 'articles', type: 'array', description: '関連記事ID配列', items: new OA\Items(type: 'integer')),
        new OA\Property(property: 'attachments', type: 'array', description: '添付ファイルID配列', items: new OA\Items(type: 'integer')),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T12:00', description: '作成日時'),
        new OA\Property(property: 'published_at', type: 'string', format: 'date-time', example: '2024-01-01T12:00', description: '公開日時'),
        new OA\Property(property: 'modified_at', type: 'string', format: 'date-time', example: '2024-01-01T12:00', description: '更新日時'),
    ]
)]
class Article {}
