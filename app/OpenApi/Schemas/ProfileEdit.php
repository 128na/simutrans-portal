<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProfileEdit',
    title: 'プロフィール編集',
    description: 'プロフィール編集情報',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1, description: 'ユーザーID'),
        new OA\Property(property: 'name', type: 'string', example: 'user123', description: 'ユーザー名'),
        new OA\Property(property: 'nickname', type: 'string', example: 'ニックネーム', description: '表示名'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com', description: 'メールアドレス'),
        new OA\Property(property: 'role', type: 'string', example: 'user', description: 'ロール'),
        new OA\Property(
            property: 'profile',
            type: 'object',
            description: 'プロフィール情報',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 1),
                new OA\Property(property: 'data', type: 'string', example: 'プロフィール本文'),
            ]
        ),
    ]
)]
class ProfileEdit {}
