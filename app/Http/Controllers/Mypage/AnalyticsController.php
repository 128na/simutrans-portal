<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\Analytics\FindArticles;
use App\Http\Requests\ArticleAnalytics\SearchRequest;
use App\Http\Resources\Mypage\ArticleAnalytic;
use App\Repositories\ArticleRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        return view('mypage.analytics', [
            'articles' => $this->articleRepository->getForAnalyticsList($user),
            'meta' => $this->metaOgpService->mypageAnalytics(),
        ]);
    }

    /**
     * アナリティクスデータを取得
     */
    #[OA\Post(
        path: '/api/v2/mypage/analytics/search',
        summary: 'アナリティクスの取得',
        description: '記事のアナリティクスデータを取得します',
        tags: ['Analytics'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'ids',
                        type: 'array',
                        description: '記事ID配列',
                        items: new OA\Items(type: 'integer')
                    ),
                    new OA\Property(
                        property: 'type',
                        type: 'string',
                        enum: ['daily', 'monthly', 'yearly'],
                        example: 'daily',
                        description: '集計タイプ'
                    ),
                    new OA\Property(
                        property: 'start_date',
                        type: 'string',
                        format: 'date',
                        example: '2024-01-01',
                        description: '開始日'
                    ),
                    new OA\Property(
                        property: 'end_date',
                        type: 'string',
                        format: 'date',
                        example: '2024-12-31',
                        description: '終了日'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: '取得成功',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'article_id', type: 'integer', example: 1),
                                    new OA\Property(property: 'title', type: 'string', example: '記事タイトル'),
                                    new OA\Property(property: 'views', type: 'integer', example: 100),
                                    new OA\Property(property: 'downloads', type: 'integer', example: 50),
                                ]
                            )
                        ),
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
        ]
    )]
    public function show(SearchRequest $searchRequest, FindArticles $findArticles): AnonymousResourceCollection
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401);
        }

        return ArticleAnalytic::collection(
            $findArticles($user, $searchRequest)
        );
    }
}
