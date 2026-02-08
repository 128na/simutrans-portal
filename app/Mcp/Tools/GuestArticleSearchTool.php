<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\FrontArticle\SearchAction;
use App\Enums\ArticlePostType;
use App\Http\Resources\Frontend\ArticleList;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GuestArticleSearchTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        未ログインで利用できる記事検索ツールです。

                ## レスポンス
                - data: 記事一覧
                    - id, title, url, thumbnail, description
                    - categories, tags, user
                    - published_at, modified_at
                - links: ページネーションリンク
                - meta: ページネーション情報
    MARKDOWN;

    public function __construct(private SearchAction $searchAction) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'word' => ['nullable', 'string', 'max:200'],
            'userIds' => ['nullable', 'array'],
            'userIds.*' => ['integer'],
            'categoryIds' => ['nullable', 'array'],
            'categoryIds.*' => ['integer'],
            'tagIds' => ['nullable', 'array'],
            'tagIds.*' => ['integer'],
            'postTypes' => ['nullable', 'array'],
            'postTypes.*' => ['string', Rule::in($this->postTypeValues())],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($validated['limit'] ?? 24);

        $condition = Arr::only($validated, [
            'word',
            'userIds',
            'categoryIds',
            'tagIds',
            'postTypes',
        ]);
        $paginator = $this->searchAction->search($condition, $limit);
        $httpRequest = app(HttpRequest::class);
        $payload = ArticleList::collection($paginator)
            ->response($httpRequest)
            ->getData(true);

        return Response::json($payload);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'word' => $schema->string()
                ->description('検索キーワード。スペース区切りでAND検索。')
                ->nullable(),
            'userIds' => $schema->array()
                ->items($schema->integer())
                ->description('投稿ユーザーIDの配列（OR検索）。')
                ->nullable(),
            'categoryIds' => $schema->array()
                ->items($schema->integer())
                ->description('カテゴリIDの配列（AND検索）。')
                ->nullable(),
            'tagIds' => $schema->array()
                ->items($schema->integer())
                ->description('タグIDの配列（OR検索）。')
                ->nullable(),
            'postTypes' => $schema->array()
                ->items($schema->string()->enum($this->postTypeValues()))
                ->description('投稿形式の配列（OR検索）。')
                ->nullable(),
            'limit' => $schema->integer()
                ->min(1)
                ->max(100)
                ->default(24)
                ->description('1ページあたりの件数。'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function postTypeValues(): array
    {
        return array_map(
            static fn (ArticlePostType $postType): string => $postType->value,
            ArticlePostType::cases()
        );
    }
}
