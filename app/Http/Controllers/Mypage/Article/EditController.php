<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage\Article;

use App\Actions\Article\UpdateArticle;
use App\Http\Controllers\Controller;
use App\Http\Requests\Article\UpdateRequest;
use App\Http\Resources\Mypage\ArticleEdit;
use App\Http\Resources\Mypage\AttachmentEdit;
use App\Http\Resources\Mypage\UserShow;
use App\Models\Article;
use App\Repositories\Article\MypageArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TagRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class EditController extends Controller
{
    public function __construct(
        private readonly MypageArticleRepository $mypageArticleRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly TagRepository $tagRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function edit(Article $article): View
    {
        $this->authorize('update', $article);
        $user = $this->loggedinUser();

        return view('mypage.article-edit', [
            'user' => new UserShow($user),
            'article' => new ArticleEdit($article->load('categories', 'tags', 'articles', 'attachments')),
            'attachments' => AttachmentEdit::collection($user->myAttachments()->with('fileInfo')->get()),
            'categories' => $this->categoryRepository->getForSearch()->groupBy('type'),
            'tags' => $this->tagRepository->getForEdit(),
            'relationalArticles' => $this->mypageArticleRepository->getForEdit($article),
            'meta' => $this->metaOgpService->mypageArticleEdit(),
        ]);
    }

    /**
     * 記事を更新
     */
    #[OA\Post(path: '/api/v2/articles/{article}', description: '既存の記事を更新します', summary: '記事の更新', security: [['sanctum' => []]], requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['article'],
            properties: [
                new OA\Property(
                    property: 'article',
                    required: ['status', 'title', 'slug', 'post_type', 'contents'],
                    properties: [
                        new OA\Property(
                            property: 'status',
                            description: 'ステータス',
                            type: 'string',
                            example: 'publish',
                            enum: ['publish', 'draft', 'private']
                        ),
                        new OA\Property(property: 'title', description: 'タイトル', type: 'string', example: '更新されたアドオン'),
                        new OA\Property(property: 'slug', description: 'スラッグ', type: 'string', example: 'updated-addon'),
                        new OA\Property(property: 'post_type', description: '投稿タイプ', type: 'string', example: 'addon-post'),
                        new OA\Property(
                            property: 'published_at',
                            description: '公開日時',
                            type: 'string',
                            format: 'date-time',
                            example: '2024-01-01T12:00'
                        ),
                        new OA\Property(property: 'contents', description: 'コンテンツデータ', type: 'object'),
                        new OA\Property(
                            property: 'categories',
                            description: 'カテゴリID配列',
                            type: 'array',
                            items: new OA\Items(type: 'integer')
                        ),
                        new OA\Property(
                            property: 'tags',
                            description: 'タグID配列',
                            type: 'array',
                            items: new OA\Items(type: 'integer')
                        ),
                        new OA\Property(
                            property: 'articles',
                            description: '関連記事ID配列',
                            type: 'array',
                            items: new OA\Items(type: 'integer')
                        ),
                        new OA\Property(
                            property: 'attachments',
                            description: '添付ファイルID配列',
                            type: 'array',
                            items: new OA\Items(type: 'integer')
                        ),
                    ],
                    type: 'object'
                ),
                new OA\Property(property: 'should_notify', description: '通知するかどうか', type: 'boolean', example: false),
            ]
        )
    ), tags: ['Articles'], parameters: [
        new OA\Parameter(
            name: 'article',
            description: '記事ID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'integer')
        ),
    ], responses: [
        new OA\Response(
            response: 200,
            description: '更新成功',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'article_id', description: '更新された記事ID', type: 'integer', example: 1),
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
    ])]
    public function update(UpdateRequest $updateRequest, Article $article, UpdateArticle $updateArticle): JsonResponse
    {
        $this->authorize('update', $article);

        /**
         * @var array{should_notify?:bool,article:array{status:string,title:string,slug:string,post_type:string,published_at?:string,contents:mixed}}
         */
        $data = $updateRequest->validated();

        $article = DB::transaction(fn (): Article => $updateArticle($article, $data));

        return response()->json(['article_id' => $article->id], 200);
    }
}
