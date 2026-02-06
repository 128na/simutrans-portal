<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\FrontArticle\SearchSuggestAction;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GuestSearchSuggestTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        未ログインで検索補助（タグ名/ユーザー名の前方一致サジェスト）を取得します。
    MARKDOWN;

    public function __construct(private SearchSuggestAction $searchSuggestAction) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in($this->allowedTypes())],
            'keyword' => ['required', 'string', 'max:100'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int) ($validated['limit'] ?? 20);
        if ($validated['type'] === 'tag') {
            $items = $this->mapTags(
                $this->searchSuggestAction->tags($validated['keyword'], $limit)
            );
        } else {
            $items = $this->mapUsers(
                $this->searchSuggestAction->users($validated['keyword'], $limit)
            );
        }

        return Response::json([
            'type' => $validated['type'],
            'items' => $items,
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
            'type' => $schema->string()
                ->enum($this->allowedTypes())
                ->description('tag または user。')
                ->required(),
            'keyword' => $schema->string()
                ->min(1)
                ->max(100)
                ->description('前方一致の検索語。')
                ->required(),
            'limit' => $schema->integer()
                ->min(1)
                ->max(100)
                ->default(20)
                ->description('取得件数。'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function allowedTypes(): array
    {
        return ['tag', 'user'];
    }

    /**
     * @param  Collection<int, Tag>  $items
     * @return array<int, array{id:int, name:string}>
     */
    private function mapTags(Collection $items): array
    {
        return $items
            ->map(fn (Tag $tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, User>  $items
     * @return array<int, array{id:int, name:string, nickname:string|null}>
     */
    private function mapUsers(Collection $items): array
    {
        return $items
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'nickname' => $user->nickname,
            ])
            ->values()
            ->all();
    }
}
