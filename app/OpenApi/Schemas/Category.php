<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="Category",
 *     title="カテゴリ",
 *     description="カテゴリ情報",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="カテゴリID"),
 *     @OA\Property(property="type", type="string", example="addon", description="カテゴリタイプ"),
 *     @OA\Property(property="slug", type="string", example="vehicles", description="スラッグ"),
 *     @OA\Property(property="name", type="string", example="乗り物", description="名前"),
 *     @OA\Property(property="description", type="string", example="乗り物関連アドオン", description="説明")
 * )
 */
final class Category {}
