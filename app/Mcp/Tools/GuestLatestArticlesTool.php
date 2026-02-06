<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\FrontArticle\LatestAction;
use App\Http\Resources\Frontend\ArticleList;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GuestLatestArticlesTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        未ログインで新着記事一覧を取得します。PAK指定で絞り込みできます。
    MARKDOWN;

    public function __construct(private LatestAction $latestAction) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'pak' => ['nullable', 'string', Rule::in($this->allowedPakValues())],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $pak = $validated['pak'] ?? 'all';
        $limit = (int) ($validated['limit'] ?? 24);

        $paginator = match ($pak) {
            'all' => $this->latestAction->allPak($limit),
            'others' => $this->latestAction->others($limit),
            default => $this->latestAction->byPak($pak, $limit),
        };

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
            'pak' => $schema->string()
                ->enum($this->allowedPakValues())
                ->description('PAK指定。all/others/128/128-japan/64 のいずれか。')
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
    private function allowedPakValues(): array
    {
        return ['all', 'others', '128', '128-japan', '64'];
    }
}
