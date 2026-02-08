<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\FrontArticle\TagCategoryAggregateAction;
use App\Models\Tag;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Collection as SupportCollection;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GuestTagCategoryAggregateTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        未ログインでタグとカテゴリ（PAK別アドオン）の件数付き一覧を取得します。

                ## レスポンス
                - tags: タグ一覧 (id, name, article_count)
                - pak_addon_categories: PAK別アドオンカテゴリ集計
                    - pak_slug: PAKスラグ
                    - addons: addon_slug, article_count
    MARKDOWN;

    public function __construct(private TagCategoryAggregateAction $aggregateAction) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $tags = $this->aggregateAction->tags()
            ->map(fn (Tag $tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
                'article_count' => (int) $tag->articles_count,
            ])
            ->values();

        $pakAddonCategories = $this->aggregateAction->pakAddonCategories()
            ->map(fn (SupportCollection $addons, string $pakSlug): array => [
                'pak_slug' => $pakSlug,
                'addons' => $addons
                    ->map(fn (\stdClass $addon): array => [
                        'addon_slug' => $addon->addon_slug,
                        'article_count' => (int) $addon->article_count,
                    ])
                    ->values(),
            ])
            ->values();

        return Response::json([
            'tags' => $tags,
            'pak_addon_categories' => $pakAddonCategories,
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
