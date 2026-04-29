<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class AttachmentController extends Controller
{
    #[OA\Get(
        path: '/api/v1/attachments',
        summary: '添付ファイル一覧取得',
        description: 'ログイン中のユーザーがアップロードした添付ファイルの一覧を返します。記事作成時のfile_id・thumbnail_id確認に使用します。',
        tags: ['Attachments'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: '取得成功',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1, description: 'attachment ID'),
                                    new OA\Property(property: 'original_name', type: 'string', example: 'addon.zip', description: '元のファイル名'),
                                    new OA\Property(property: 'is_image', type: 'boolean', example: false, description: '画像ファイルかどうか'),
                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T12:00:00+09:00', description: 'アップロード日時'),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: '認証エラー', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function index(): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        $attachments = $user->myAttachments()->latest()->get();

        return response()->json([
            'data' => $attachments->map(fn (Attachment $a): array => [
                'id' => $a->id,
                'original_name' => $a->original_name,
                'is_image' => $a->is_image,
                'created_at' => $a->created_at?->toIso8601String(),
            ])->values()->all(),
        ]);
    }
}
