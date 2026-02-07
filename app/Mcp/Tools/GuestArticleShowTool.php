<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\FrontArticle\ShowAction;
use App\Http\Resources\Frontend\ArticleShow;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Http\Request as HttpRequest;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GuestArticleShowTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        未ログインで公開記事の詳細を取得します。
    MARKDOWN;

    public function __construct(private ShowAction $showAction) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'userIdOrNickname' => ['required', 'string', 'max:100'],
            'articleSlug' => ['required', 'string', 'max:200'],
        ]);

        $article = ($this->showAction)(
            $validated['userIdOrNickname'],
            $validated['articleSlug']
        );

        if ($article === null) {
            return Response::error('Article not found.');
        }

        $httpRequest = app(HttpRequest::class);
        $payload = ArticleShow::make($article)
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
            'userIdOrNickname' => $schema->string()
                ->min(1)
                ->max(100)
                ->required()
                ->description('ユーザーIDまたはニックネーム。'),
            'articleSlug' => $schema->string()
                ->min(1)
                ->max(200)
                ->required()
                ->description('記事のslug。'),
        ];
    }
}
