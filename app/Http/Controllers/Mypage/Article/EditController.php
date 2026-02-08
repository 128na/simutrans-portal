<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage\Article;

use App\Actions\Article\UpdateArticle;
use App\Http\Requests\Article\UpdateRequest;
use App\Http\Resources\Mypage\ArticleEdit;
use App\Http\Resources\Mypage\AttachmentEdit;
use App\Http\Resources\Mypage\UserShow;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class EditController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly TagRepository $tagRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function edit(Article $article): View
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        if ($user->cannot('update', $article)) {
            abort(403);
        }

        return view('mypage.article-edit', [
            'user' => new UserShow($user),
            'article' => new ArticleEdit($article->load('categories', 'tags', 'articles', 'attachments')),
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo')->get()),
            'categories' => $this->categoryRepository->getForSearch()->groupBy('type'),
            'tags' => $this->tagRepository->getForEdit(),
            'relationalArticles' => $this->articleRepository->getForEdit($article),
            'meta' => $this->metaOgpService->mypageArticleEdit(),
        ]);
    }

    /**
     * 記事を更新
     */
    #[OA\Post(
        path: '/api/v2/articles/{article}',
        summary: '記事の更新',
        description: '既存の記事を更新します',
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['article'],
                properties: [
                    new OA\Property(
                        property: 'article',
                        type: 'object',
                        required: ['status', 'title', 'slug', 'post_type', 'contents'],
                        properties: [
                            new OA\Property(
                                property: 'status',
                                type: 'string',
                                example: 'publish',
                                description: 'ステータス',
                                enum: ['publish', 'draft', 'private']
                            ),
                            new OA\Property(property: 'title', type: 'string', example: '更新されたアドオン', description: 'タイトル'),
                            new OA\Property(property: 'slug', type: 'string', example: 'updated-addon', description: 'スラッグ'),
                            new OA\Property(property: 'post_type', type: 'string', example: 'addon-post', description: '投稿タイプ'),
                            new OA\Property(
                                property: 'published_at',
                                type: 'string',
                                format: 'date-time',
                                example: '2024-01-01T12:00',
                                description: '公開日時'
                            ),
                            new OA\Property(property: 'contents', type: 'object', description: 'コンテンツデータ'),
                            new OA\Property(
                                property: 'categories',
                                type: 'array',
                                description: 'カテゴリID配列',
                                items: new OA\Items(type: 'integer')
                            ),
                            new OA\Property(
                                property: 'tags',
                                type: 'array',
                                description: 'タグID配列',
                                items: new OA\Items(type: 'integer')
                            ),
                            new OA\Property(
                                property: 'articles',
                                type: 'array',
                                description: '関連記事ID配列',
                                items: new OA\Items(type: 'integer')
                            ),
                            new OA\Property(
                                property: 'attachments',
                                type: 'array',
                                description: '添付ファイルID配列',
                                items: new OA\Items(type: 'integer')
                            ),
                        ]
                    ),
                    new OA\Property(property: 'should_notify', type: 'boolean', example: false, description: '通知するかどうか'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '更新成功',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'article_id', type: 'integer', example: 1, description: '更新された記事ID'),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'バリデーションエラー',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Validation error'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: '権限エラー',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Forbidden'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: '記事が見つからない',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Not found'),
                    ]
                )
            ),
        ]
    )]
    public function update(UpdateRequest $updateRequest, Article $article, UpdateArticle $updateArticle): JsonResponse
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        if ($user->cannot('update', $article)) {
            abort(403);
        }

        /**
         * @var array{should_notify?:bool,article:array{status:string,title:string,slug:string,post_type:string,published_at?:string,contents:mixed}}
         */
        $data = $updateRequest->validated();

        $article = DB::transaction(fn (): Article => $updateArticle($article, $data));

        return response()->json(['article_id' => $article->id], 200);
    }
}
