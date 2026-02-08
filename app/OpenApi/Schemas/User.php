<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    title: 'ユーザー',
    description: 'ユーザー情報',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1, description: 'ユーザーID'),
        new OA\Property(property: 'name', type: 'string', example: 'user123', description: 'ユーザー名'),
        new OA\Property(property: 'nickname', type: 'string', example: 'ニックネーム', description: '表示名'),
        new OA\Property(property: 'role', type: 'string', example: 'user', description: 'ロール', enum: ['user', 'admin']),
        new OA\Property(
            property: 'profile',
            type: 'object',
            description: 'プロフィール情報',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'data', type: 'string', example: 'プロフィール本文'),
                new OA\Property(
                    property: 'attachments',
                    type: 'array',
                    description: 'プロフィール添付ファイル',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'thumbnail', type: 'string'),
                            new OA\Property(property: 'original_name', type: 'string'),
                            new OA\Property(property: 'url', type: 'string'),
                        ]
                    )
                ),
            ]
        ),
    ]
)]
class User {}
