<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Category',
    title: 'カテゴリ',
    description: 'カテゴリ情報',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1, description: 'カテゴリID'),
        new OA\Property(property: 'type', type: 'string', example: 'addon', description: 'カテゴリタイプ'),
        new OA\Property(property: 'slug', type: 'string', example: 'vehicles', description: 'スラッグ'),
        new OA\Property(property: 'name', type: 'string', example: '乗り物', description: '名前'),
        new OA\Property(property: 'description', type: 'string', example: '乗り物関連アドオン', description: '説明'),
    ]
)]
class Category {}
