<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Actions\Analytics\FindArticles;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleAnalytics\SearchRequest;
use App\Http\Resources\Mypage\ArticleAnalytic;
use App\Repositories\Article\MypageArticleRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly MypageArticleRepository $mypageArticleRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): View
    {
        $user = $this->loggedinUser();

        return view('mypage.analytics', [
            'articles' => $this->mypageArticleRepository->getForAnalyticsList($user),
            'meta' => $this->metaOgpService->mypageAnalytics(),
        ]);
    }

    /**
     * アナリティクスデータを取得
     */
    #[OA\Post(path: '/api/v2/mypage/analytics/search', description: '記事のアナリティクスデータを取得します', summary: 'アナリティクスの取得', security: [['sanctum' => []]], requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'ids',
                    description: '記事ID配列',
                    type: 'array',
                    items: new OA\Items(type: 'integer')
                ),
                new OA\Property(
                    property: 'type',
                    description: '集計タイプ',
                    type: 'string',
                    example: 'daily',
                    enum: ['daily', 'monthly', 'yearly']
                ),
                new OA\Property(
                    property: 'start_date',
                    description: '開始日',
                    type: 'string',
                    format: 'date',
                    example: '2024-01-01'
                ),
                new OA\Property(
                    property: 'end_date',
                    description: '終了日',
                    type: 'string',
                    format: 'date',
                    example: '2024-12-31'
                ),
            ]
        )
    ), tags: ['Analytics'], responses: [
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
                                new OA\Property(property: 'article_id', type: 'integer', example: 1),
                                new OA\Property(property: 'title', type: 'string', example: '記事タイトル'),
                                new OA\Property(property: 'views', type: 'integer', example: 100),
                                new OA\Property(property: 'downloads', type: 'integer', example: 50),
                            ],
                            type: 'object'
                        )
                    ),
                ],
                type: 'object'
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
    ])]
    public function show(SearchRequest $searchRequest, FindArticles $findArticles): AnonymousResourceCollection
    {
        $user = $this->loggedinUser();

        return ArticleAnalytic::collection(
            $findArticles($user, $searchRequest)
        );
    }
}
