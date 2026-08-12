<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage\Article;

use App\Actions\Article\DeleteArticle;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class DestroyController extends Controller
{
    /**
     * 記事を削除
     */
    #[OA\Delete(
        path: '/api/v2/articles/{article}',
        summary: '記事の削除',
        description: '指定された記事を削除します（論理削除）',
        tags: ['Articles'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'article',
                in: 'path',
                required: true,
                description: '記事ID',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: '削除成功',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'article_id', type: 'integer', example: 1, description: '削除された記事ID'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: '認証エラー',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 403,
                description: '権限エラー',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
            new OA\Response(
                response: 404,
                description: '記事が見つかりません',
                content: new OA\JsonContent(ref: '#/components/schemas/Error')
            ),
        ]
    )]
    public function destroy(Article $article, DeleteArticle $deleteArticle): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        if ($user->cannot('delete', $article)) {
            abort(403);
        }

        $deleteArticle($article);

        return response()->json(['article_id' => $article->id], 200);
    }
}
