<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\ArticleStatus;
use App\Events\Article\ArticleUpdated;
use App\Http\Controllers\Controller;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class ArticleStatusController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $repository,
    ) {}

    #[OA\Patch(
        path: '/api/v1/articles/{id}/status',
        summary: '記事ステータス変更',
        description: 'ログイン中のユーザーの記事のステータスのみを変更します。タイトル・本文は変更しません。',
        tags: ['Articles'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: '記事ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(property: 'status', type: 'string', example: 'draft', description: '新しいステータス', enum: ['publish', 'draft', 'private', 'trash']),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '更新成功',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'title', type: 'string', example: '記事タイトル'),
                        new OA\Property(property: 'slug', type: 'string', example: 'my-article'),
                        new OA\Property(property: 'status', type: 'string', example: 'draft'),
                        new OA\Property(property: 'post_type', type: 'string', example: 'markdown'),
                        new OA\Property(property: 'published_at', type: 'string', nullable: true, example: '2024/01/01 12:00'),
                        new OA\Property(property: 'modified_at', type: 'string', nullable: true, example: '2024/01/01 12:00'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: '認証エラー', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: '記事が見つかりません', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 422, description: 'バリデーションエラー', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in([
                ArticleStatus::Publish->value,
                ArticleStatus::Draft->value,
                ArticleStatus::Private->value,
                ArticleStatus::Trash->value,
            ])],
        ]);

        try {
            $article = Article::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $newStatus = ArticleStatus::from($validated['status']);
        $notYetPublished = is_null($article->published_at);

        $updateData = [
            'status' => $newStatus,
            'modified_at' => CarbonImmutable::now()->toDateTimeString(),
        ];

        if ($notYetPublished && $newStatus === ArticleStatus::Publish) {
            $updateData['published_at'] = CarbonImmutable::now()->toDateTimeString();
        }

        $this->repository->update($article, $updateData);
        $article->refresh();

        dispatch(new JobUpdateRelated($article->id));
        event(new ArticleUpdated($article, false, false));

        return response()->json([
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'status' => $article->status->value,
            'post_type' => $article->post_type->value,
            'published_at' => $article->published_at?->format('Y/m/d H:i'),
            'modified_at' => $article->modified_at?->format('Y/m/d H:i'),
        ]);
    }
}
