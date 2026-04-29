<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\Article\StoreArticle;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UserArticleCreateAddonPostTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        アドオン投稿形式の記事を新規作成します。
        アドオンファイル（zip等）を添付してポータルで直接配布する形式です。

        ## 事前準備
        投稿するファイルをあらかじめWebUI（マイページ > 添付ファイル管理）または
        POST /api/v2/attachments でアップロードしてください。
        アップロード済みファイルのIDは user-attachment-list で確認できます。

        ## カテゴリ・タグ
        categories・tags の ID は guest-article-search-options-tool で確認できます。
        categories には pak 型・addon 型などのカテゴリIDを指定できます（page 型は不可）。

        ## パラメータ
        - title: 記事タイトル
        - slug: URLスラッグ（英数字・ハイフン・アンダースコアのみ、自分の記事内で一意）
        - status: publish（公開）または draft（下書き）
        - categories: カテゴリIDの配列
        - tags: タグIDの配列（空配列可）
        - file_id: アドオンファイルのattachment ID（user-attachment-listで取得）
        - description: 説明文（max:2048）
        - author: 作者名（任意、max:255）
        - thanks: 謝辞（任意、max:2048）
        - license: ライセンス（任意、max:2048）
        - thumbnail_id: サムネイル画像のattachment ID（任意）

        ## レスポンス
        - id: 作成された記事ID
        - title: タイトル
        - slug: スラッグ
        - status: ステータス
        - post_type: 投稿形式（addon-post固定）
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
            'file_id' => ['required', 'integer'],
            'description' => ['required', 'string', 'max:2048'],
            'author' => ['nullable', 'string', 'max:255'],
            'thanks' => ['nullable', 'string', 'max:2048'],
            'license' => ['nullable', 'string', 'max:2048'],
            'thumbnail_id' => ['nullable', 'integer'],
        ]);

        if (! Attachment::where('id', $validated['file_id'])->where('user_id', $user->id)->exists()) {
            return Response::error('file_id not found or does not belong to you. Use user-attachment-list to find your attachment IDs.');
        }

        $contents = [
            'file' => $validated['file_id'],
            'description' => $validated['description'],
            'author' => $validated['author'] ?? null,
            'thanks' => $validated['thanks'] ?? null,
            'license' => $validated['license'] ?? null,
        ];
        if (isset($validated['thumbnail_id'])) {
            $contents['thumbnail'] = $validated['thumbnail_id'];
        }

        $article = DB::transaction(fn (): Article => ($this->storeArticle)($user, [
            'article' => [
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'post_type' => ArticlePostType::AddonPost->value,
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
            'file_id' => $schema->integer()
                ->required()
                ->description('アドオンファイルのattachment ID。user-attachment-listで取得したIDを指定します。'),
            'description' => $schema->string()
                ->required()
                ->description('アドオンの説明文（max:2048）。'),
            'author' => $schema->string()
                ->nullable()
                ->description('作者名（任意、max:255）。'),
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
