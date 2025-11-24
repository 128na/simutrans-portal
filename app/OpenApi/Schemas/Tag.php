<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="Tag",
 *     title="タグ",
 *     description="タグ情報",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="タグID"),
 *     @OA\Property(property="name", type="string", example="pak128.japan", description="タグ名"),
 *     @OA\Property(property="description", type="string", example="pak128.japan用アドオン", description="説明"),
 *     @OA\Property(property="editable", type="boolean", example=true, description="編集可能かどうか"),
 *     @OA\Property(
 *         property="created_by",
 *         type="object",
 *         nullable=true,
 *         description="作成者",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(
 *         property="last_modified_by",
 *         type="object",
 *         nullable=true,
 *         description="最終更新者",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(property="last_modified_at", type="string", format="date-time", example="2024-01-01T12:00", description="最終更新日時"),
 *     @OA\Property(property="articles_count", type="integer", example=10, description="記事数")
 * )
 */
final class Tag {}
