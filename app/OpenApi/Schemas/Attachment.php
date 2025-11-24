<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="Attachment",
 *     title="添付ファイル",
 *     description="添付ファイル情報",
 *
 *     @OA\Property(property="id", type="integer", example=1, description="添付ファイルID"),
 *     @OA\Property(property="attachmentable_type", type="string", example="Article", description="関連エンティティタイプ"),
 *     @OA\Property(property="attachmentable_id", type="integer", example=1, description="関連エンティティID"),
 *     @OA\Property(property="type", type="string", example="image/png", description="ファイルタイプ"),
 *     @OA\Property(property="original_name", type="string", example="sample.pak", description="オリジナルファイル名"),
 *     @OA\Property(property="thumbnail", type="string", example="https://example.com/thumb.jpg", description="サムネイルURL"),
 *     @OA\Property(property="url", type="string", example="https://example.com/file.pak", description="ファイルURL"),
 *     @OA\Property(property="size", type="integer", example=1024, description="ファイルサイズ(bytes)"),
 *     @OA\Property(
 *         property="fileInfo",
 *         type="object",
 *         nullable=true,
 *         description="ファイル情報",
 *         @OA\Property(property="data", type="object", description="ファイルメタデータ")
 *     ),
 *     @OA\Property(property="caption", type="string", example="キャプション", description="画像キャプション（画像のみ）"),
 *     @OA\Property(property="order", type="integer", example=1, description="表示順（画像のみ）"),
 *     @OA\Property(
 *         property="attachmentable",
 *         type="object",
 *         nullable=true,
 *         description="関連記事情報",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="title", type="string")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z", description="作成日時")
 * )
 */
final class Attachment {}
