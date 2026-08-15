<?php

declare(strict_types=1);

namespace App\OpenApi\Paths;

use OpenApi\Attributes as OA;

/**
 * `App\Http\Controllers\Mypage\Article\CreateController::store()` のOpenAPI定義。
 * swagger-phpは属性が実際のルートを処理するメソッドに付いている必要はなく、
 * スキャン対象パス内であればどこにあっても`path`/`method`で拾われるため、
 * コントローラー本体の可読性を優先しここへ分離している。
 */
#[OA\Post(path: '/api/v2/articles', description: '新しい記事を作成します', summary: '記事の作成', security: [['sanctum' => []]], requestBody: new OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
        required: ['article'],
        properties: [
            new OA\Property(
                property: 'article',
                required: ['status', 'title', 'slug', 'post_type', 'contents'],
                properties: [
                    new OA\Property(
                        property: 'status',
                        description: 'ステータス',
                        type: 'string',
                        example: 'publish',
                        enum: ['publish', 'draft', 'private']
                    ),
                    new OA\Property(property: 'title', description: 'タイトル', type: 'string', example: '新しいアドオン'),
                    new OA\Property(property: 'slug', description: 'スラッグ', type: 'string', example: 'new-addon'),
                    new OA\Property(property: 'post_type', description: '投稿タイプ', type: 'string', example: 'addon-post'),
                    new OA\Property(
                        property: 'published_at',
                        description: '公開日時',
                        type: 'string',
                        format: 'date-time',
                        example: '2024-01-01T12:00'
                    ),
                    new OA\Property(property: 'contents', description: 'コンテンツデータ', type: 'object'),
                    new OA\Property(
                        property: 'categories',
                        description: 'カテゴリID配列',
                        type: 'array',
                        items: new OA\Items(type: 'integer')
                    ),
                    new OA\Property(
                        property: 'tags',
                        description: 'タグID配列',
                        type: 'array',
                        items: new OA\Items(type: 'integer')
                    ),
                    new OA\Property(
                        property: 'articles',
                        description: '関連記事ID配列',
                        type: 'array',
                        items: new OA\Items(type: 'integer')
                    ),
                    new OA\Property(
                        property: 'attachments',
                        description: '添付ファイルID配列',
                        type: 'array',
                        items: new OA\Items(type: 'integer')
                    ),
                ],
                type: 'object'
            ),
            new OA\Property(property: 'should_notify', description: '通知するかどうか', type: 'boolean', example: true),
        ]
    )
), tags: ['Articles'], responses: [
    new OA\Response(
        response: 200,
        description: '作成成功',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'article_id', description: '作成された記事ID', type: 'integer', example: 1),
            ]
        )
    ),
    new OA\Response(
        response: 400,
        description: 'バリデーションエラー',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Validation error'),
                new OA\Property(property: 'errors', type: 'object'),
            ]
        )
    ),
    new OA\Response(
        response: 403,
        description: '権限エラー',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Forbidden'),
            ]
        )
    ),
])]
class ArticleCreatePath {}
