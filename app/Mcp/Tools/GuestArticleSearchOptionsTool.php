<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\FrontArticle\SearchAction;
use App\Enums\ArticlePostType;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GuestArticleSearchOptionsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        未ログインで記事検索に使う選択肢（カテゴリ・タグ・ユーザー・投稿形式）を取得します。

        ## レスポンス
        - categories: カテゴリ一覧 (id, type, slug, need_admin)
        - tags: タグ一覧 (id, name)
        - users: ユーザー一覧 (id, name, nickname)
        - postTypes: 投稿形式一覧 (value, name)
    MARKDOWN;

    public function __construct(private SearchAction $searchAction) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $options = $this->searchAction->options();

        $categories = $options['categories']
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'type' => $category->type->value,
                'slug' => $category->slug,
                'need_admin' => $category->need_admin,
            ])
            ->values();

        $tags = $options['tags']
            ->map(fn (Tag $tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
            ])
            ->values();

        $users = $options['users']
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'nickname' => $user->nickname,
            ])
            ->values();

        $postTypes = array_map(
            static fn (ArticlePostType $postType): array => [
                'value' => $postType->value,
                'name' => $postType->name,
            ],
            ArticlePostType::cases()
        );

        return Response::json([
            'categories' => $categories,
            'tags' => $tags,
            'users' => $users,
            'postTypes' => $postTypes,
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
