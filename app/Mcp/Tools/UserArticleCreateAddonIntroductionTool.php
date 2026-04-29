<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\Article\StoreArticle;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UserArticleCreateAddonIntroductionTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        アドオン紹介形式の記事を新規作成します。
        外部サイト（planet.simutrans.com など）に投稿済みのアドオンをポータルで紹介する形式です。

        ## カテゴリ・タグ
        categories・tags の ID は guest-article-search-options-tool で確認できます。
        categories には pak 型・addon 型などのカテゴリIDを指定できます（page 型は不可）。

        ## パラメータ
        - title: 記事タイトル
        - slug: URLスラッグ（英数字・ハイフン・アンダースコアのみ、自分の記事内で一意）
        - status: publish（公開）または draft（下書き）
        - categories: カテゴリIDの配列
        - tags: タグIDの配列（空配列可）
        - link: 外部サイトのURL（max:255）
        - author: 作者名（max:255）
        - description: 説明文（max:2048）
        - thanks: 謝辞（任意、max:2048）
        - license: ライセンス（任意、max:2048）
        - thumbnail_id: サムネイル画像のattachment ID（任意）

        ## レスポンス
        - id: 作成された記事ID
        - title: タイトル
        - slug: スラッグ
        - status: ステータス
        - post_type: 投稿形式（addon-introduction固定）
        - published_at: 公開日時（公開時のみ）
        - modified_at: 更新日時
    MARKDOWN;

    public function __construct(private readonly StoreArticle $storeArticle) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return Response::error('Unauthorized.');
        }

        if ($user->cannot('store', Article::class)) {
            return Response::error('Forbidden.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\-_]+$/',
                Rule::unique('articles')->where('user_id', $user->id),
            ],
            'status' => ['required', 'string', Rule::in([ArticleStatus::Publish->value, ArticleStatus::Draft->value])],
            'categories' => ['present', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'tags' => ['present', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'link' => ['required', 'url', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2048'],
            'thanks' => ['nullable', 'string', 'max:2048'],
            'license' => ['nullable', 'string', 'max:2048'],
            'thumbnail_id' => ['nullable', 'integer'],
        ]);

        $contents = [
            'link' => $validated['link'],
            'author' => $validated['author'],
            'description' => $validated['description'],
            'thanks' => $validated['thanks'] ?? null,
            'license' => $validated['license'] ?? null,
            'agreement' => true,
            'exclude_link_check' => false,
        ];
        if (isset($validated['thumbnail_id'])) {
            $contents['thumbnail'] = $validated['thumbnail_id'];
        }

        $article = DB::transaction(fn (): Article => ($this->storeArticle)($user, [
            'article' => [
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'post_type' => ArticlePostType::AddonIntroduction->value,
                'status' => $validated['status'],
                'contents' => $contents,
                'categories' => $validated['categories'],
                'tags' => $validated['tags'],
            ],
            'should_notify' => false,
        ]));

        return Response::json([
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'status' => $article->status->value,
            'post_type' => $article->post_type->value,
            'published_at' => $article->published_at?->format('Y/m/d H:i'),
            'modified_at' => $article->modified_at?->format('Y/m/d H:i'),
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
            'title' => $schema->string()
                ->required()
                ->description('記事タイトル。'),
            'slug' => $schema->string()
                ->required()
                ->description('URLスラッグ。英数字・ハイフン・アンダースコアのみ使用可。自分の記事内で一意である必要があります。'),
            'status' => $schema->string()
                ->enum([ArticleStatus::Publish->value, ArticleStatus::Draft->value])
                ->required()
                ->description('公開ステータス。publish（公開）または draft（下書き）。'),
            'categories' => $schema->array()
                ->items($schema->integer())
                ->required()
                ->description('カテゴリIDの配列。guest-article-search-options-toolで確認してください（pak・addon等のtype）。'),
            'tags' => $schema->array()
                ->items($schema->integer())
                ->required()
                ->description('タグIDの配列。guest-article-search-options-toolで確認してください。空配列可。'),
            'link' => $schema->string()
                ->required()
                ->description('アドオンが公開されている外部サイトのURL（max:255）。'),
            'author' => $schema->string()
                ->required()
                ->description('作者名（max:255）。'),
            'description' => $schema->string()
                ->required()
                ->description('アドオンの説明文（max:2048）。'),
            'thanks' => $schema->string()
                ->nullable()
                ->description('謝辞（任意、max:2048）。'),
            'license' => $schema->string()
                ->nullable()
                ->description('ライセンス情報（任意、max:2048）。'),
            'thumbnail_id' => $schema->integer()
                ->nullable()
                ->description('サムネイル画像のattachment ID（任意）。user-attachment-listで取得した画像IDを指定します。'),
        ];
    }
}
