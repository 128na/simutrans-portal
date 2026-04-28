<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class UserMyArticleShowTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        ログイン中のユーザーが投稿した記事の詳細を取得します。下書き・非公開記事も取得可能です。

        ## レスポンス
        - id: 記事ID
        - title: タイトル
        - slug: スラッグ
        - status: ステータス（publish / draft / private / trash）
        - post_type: 投稿形式（addon-post / addon-introduction / page / markdown）
        - published_at: 公開日時
        - modified_at: 更新日時
        - categories: カテゴリ一覧 (id, slug, type)
        - tags: タグ一覧 (id, name)
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return Response::error('Unauthorized.');
        }

        $validated = $request->validate([
            'article_id' => ['required', 'integer'],
        ]);

        try {
            $article = Article::where('id', $validated['article_id'])
                ->where('user_id', $user->id)
                ->with(['categories', 'tags'])
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return Response::error('Article not found.');
        }

        return Response::json([
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'status' => $article->status->value,
            'post_type' => $article->post_type->value,
            'published_at' => $article->published_at?->format('Y/m/d H:i'),
            'modified_at' => $article->modified_at?->format('Y/m/d H:i'),
            'categories' => $article->categories->map(fn (Category $c): array => [
                'id' => $c->id,
                'slug' => $c->slug,
                'type' => $c->type->value,
            ])->values()->all(),
            'tags' => $article->tags->map(fn (Tag $t): array => [
                'id' => $t->id,
                'name' => $t->name,
            ])->values()->all(),
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'article_id' => $schema->integer()
                ->required()
                ->description('記事ID。user-my-article-listで取得したidを指定します。'),
        ];
    }
}
