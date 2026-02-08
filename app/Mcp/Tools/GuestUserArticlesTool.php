<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\FrontArticle\UserArticlesAction;
use App\Http\Resources\Frontend\ArticleList;
use App\Http\Resources\Frontend\UserShow;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request as HttpRequest;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GuestUserArticlesTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        未ログインでユーザー別の公開記事一覧を取得します。

                ## レスポンス
                - user: ユーザー情報 (id, name, nickname)
                - articles: 記事一覧
                    - data: id, title, url, thumbnail, description
                    - categories, tags, user
                    - published_at, modified_at
                    - links, meta
    MARKDOWN;

    public function __construct(private UserArticlesAction $userArticlesAction) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'userIdOrNickname' => ['required', 'string', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($validated['limit'] ?? 24);

        try {
            $result = ($this->userArticlesAction)(
                $validated['userIdOrNickname'],
                $limit
            );
        } catch (ModelNotFoundException) {
            return Response::error('User not found.');
        }

        $httpRequest = app(HttpRequest::class);
        $articles = ArticleList::collection($result['articles'])
            ->response($httpRequest)
            ->getData(true);

        return Response::json([
            'user' => UserShow::make($result['user'])->resolve($httpRequest),
            'articles' => $articles,
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'userIdOrNickname' => $schema->string()
                ->min(1)
                ->max(100)
                ->required()
                ->description('ユーザーIDまたはニックネーム。'),
            'limit' => $schema->integer()
                ->min(1)
                ->max(100)
                ->default(24)
                ->description('1ページあたりの件数。'),
        ];
    }
}
