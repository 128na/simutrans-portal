<?php

declare(strict_types=1);

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="ユーザー",
 *     description="ユーザー情報",
 *     @OA\Property(property="id", type="integer", example=1, description="ユーザーID"),
 *     @OA\Property(property="name", type="string", example="user123", description="ユーザー名"),
 *     @OA\Property(property="nickname", type="string", example="ニックネーム", description="表示名"),
 *     @OA\Property(property="role", type="string", example="user", description="ロール", enum={"user", "admin"}),
 *     @OA\Property(
 *         property="profile",
 *         type="object",
 *         description="プロフィール情報",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="data", type="string", example="プロフィール本文"),
 *         @OA\Property(
 *             property="attachments",
 *             type="array",
 *             description="プロフィール添付ファイル",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="thumbnail", type="string"),
 *                 @OA\Property(property="original_name", type="string"),
 *                 @OA\Property(property="url", type="string")
 *             )
 *         )
 *     )
 * )
 */
final class User {}
