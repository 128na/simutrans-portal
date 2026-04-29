<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Repositories\Article\MypageArticleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ArticleController extends Controller
{
    public function __construct(
        private readonly MypageArticleRepository $repository,
    ) {}

    #[OA\Get(
        path: '/api/v1/articles',
        summary: '自分の記事一覧取得',
        description: 'ログイン中のユーザーの記事一覧を返します。下書き・非公開記事も含みます。',
        tags: ['Articles'],
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
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'title', type: 'string', example: '記事タイトル'),
                                    new OA\Property(property: 'slug', type: 'string', example: 'my-article'),
                                    new OA\Property(property: 'status', type: 'string', example: 'publish', enum: ['publish', 'draft', 'private', 'trash']),
                                    new OA\Property(property: 'post_type', type: 'string', example: 'markdown', enum: ['addon-post', 'addon-introduction', 'page', 'markdown']),
                                    new OA\Property(property: 'published_at', type: 'string', nullable: true, example: '2024/01/01 12:00'),
                                    new OA\Property(property: 'modified_at', type: 'string', nullable: true, example: '2024/01/01 12:00'),
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

        $articles = $this->repository->getForMypageList($user);

        return response()->json([
            'data' => $articles->map(fn (Article $article): array => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'status' => $article->status->value,
                'post_type' => $article->post_type->value,
                'published_at' => $article->published_at?->format('Y/m/d H:i'),
                'modified_at' => $article->modified_at?->format('Y/m/d H:i'),
            ])->values()->all(),
        ]);
    }

    #[OA\Get(
        path: '/api/v1/articles/{id}',
        summary: '自分の記事詳細取得',
        description: 'ログイン中のユーザーの記事詳細を返します。下書き・非公開記事も取得可能です。',
        tags: ['Articles'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: '記事ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: '取得成功',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'title', type: 'string', example: '記事タイトル'),
                        new OA\Property(property: 'slug', type: 'string', example: 'my-article'),
                        new OA\Property(property: 'status', type: 'string', example: 'publish'),
                        new OA\Property(property: 'post_type', type: 'string', example: 'markdown'),
                        new OA\Property(property: 'published_at', type: 'string', nullable: true, example: '2024/01/01 12:00'),
                        new OA\Property(property: 'modified_at', type: 'string', nullable: true, example: '2024/01/01 12:00'),
                        new OA\Property(property: 'categories', type: 'array', items: new OA\Items(properties: [new OA\Property(property: 'id', type: 'integer'), new OA\Property(property: 'slug', type: 'string'), new OA\Property(property: 'type', type: 'string')])),
                        new OA\Property(property: 'tags', type: 'array', items: new OA\Items(properties: [new OA\Property(property: 'id', type: 'integer'), new OA\Property(property: 'name', type: 'string')])),
                    ]
                )
            ),
            new OA\Response(response: 401, description: '認証エラー', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: '記事が見つかりません', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        try {
            $article = Article::where('id', $id)
                ->where('user_id', $user->id)
                ->with(['categories', 'tags'])
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return response()->json([
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'status' => $article->status->value,
            'post_type' => $article->post_type->value,
            'published_at' => $article->published_at?->format('Y/m/d H:i'),
            'modified_at' => $article->modified_at?->format('Y/m/d H:i'),
            'categories' => $article->categories->map(fn (Category $c): array => [
                'id' => $c->id,
                'slug' => $c->slug,
                'type' => $c->type->value,
            ])->values()->all(),
            'tags' => $article->tags->map(fn (Tag $t): array => [
                'id' => $t->id,
                'name' => $t->name,
            ])->values()->all(),
        ]);
    }
}
