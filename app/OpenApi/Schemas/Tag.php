<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Tag',
    title: 'タグ',
    description: 'タグ情報',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1, description: 'タグID'),
        new OA\Property(property: 'name', type: 'string', example: 'pak128.japan', description: 'タグ名'),
        new OA\Property(property: 'description', type: 'string', example: 'pak128.japan用アドオン', description: '説明'),
        new OA\Property(property: 'editable', type: 'boolean', example: true, description: '編集可能かどうか'),
        new OA\Property(
            property: 'created_by',
            type: 'object',
            nullable: true,
            description: '作成者',
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'name', type: 'string'),
            ]
        ),
        new OA\Property(
            property: 'last_modified_by',
            type: 'object',
            nullable: true,
            description: '最終更新者',
            properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'name', type: 'string'),
            ]
        ),
        new OA\Property(property: 'last_modified_at', type: 'string', format: 'date-time', example: '2024-01-01T12:00', description: '最終更新日時'),
        new OA\Property(property: 'articles_count', type: 'integer', example: 10, description: '記事数'),
    ]
)]
class Tag {}
