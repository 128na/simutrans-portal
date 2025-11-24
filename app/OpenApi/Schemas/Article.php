<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="Article",
 *     title="記事",
 *     description="記事情報",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="記事ID"),
 *     @OA\Property(property="title", type="string", example="新しいアドオン", description="タイトル"),
 *     @OA\Property(property="slug", type="string", example="new-addon", description="スラッグ"),
 *     @OA\Property(property="status", type="string", example="publish", description="ステータス", enum={"publish", "draft", "private"}),
 *     @OA\Property(property="post_type", type="string", example="addon-post", description="投稿タイプ"),
 *     @OA\Property(property="contents", type="object", description="コンテンツデータ"),
 *     @OA\Property(property="categories", type="array", description="カテゴリID配列", @OA\Items(type="integer")),
 *     @OA\Property(property="tags", type="array", description="タグID配列", @OA\Items(type="integer")),
 *     @OA\Property(property="articles", type="array", description="関連記事ID配列", @OA\Items(type="integer")),
 *     @OA\Property(property="attachments", type="array", description="添付ファイルID配列", @OA\Items(type="integer")),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00", description="作成日時"),
 *     @OA\Property(property="published_at", type="string", format="date-time", example="2024-01-01T12:00", description="公開日時"),
 *     @OA\Property(property="modified_at", type="string", format="date-time", example="2024-01-01T12:00", description="更新日時")
 * )
 */
final class Article {}
