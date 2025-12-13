<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="Error",
 *     title="エラー",
 *     description="エラーレスポンス",
 *
 *     @OA\Property(property="message", type="string", example="An error occurred", description="エラーメッセージ"),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         description="バリデーションエラー詳細",
 *         example={"field": {"The field is required"}}
 *     )
 * )
 */
class Error {}
