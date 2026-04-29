<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\Article\StoreArticle;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UserArticleCreateTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Markdownタイプの記事を新規作成します。
        作成できるのは Markdown 形式の記事のみです。アドオン投稿やページ記事は作成できません。

        ## パラメータ
        - title: 記事タイトル
        - slug: URLスラッグ（英数字・ハイフン・アンダースコアのみ、自分の記事内で一意）
        - markdown: Markdown形式の本文
        - status: publish（公開）または draft（下書き）
        - thumbnail_id: サムネイル画像のattachment ID（任意）

        ## レスポンス
        - id: 作成された記事ID
        - title: タイトル
        - slug: スラッグ
        - status: ステータス
        - post_type: 投稿形式（markdown固定）
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

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\-_]+$/',
                Rule::unique('articles')->where('user_id', $user->id),
            ],
            'markdown' => ['required', 'string'],
            'status' => ['required', 'string', Rule::in([ArticleStatus::Publish->value, ArticleStatus::Draft->value])],
            'thumbnail_id' => ['nullable', 'integer'],
        ]);

        $contents = ['markdown' => $validated['markdown']];
        if (isset($validated['thumbnail_id'])) {
            $contents['thumbnail'] = $validated['thumbnail_id'];
        }

        $article = ($this->storeArticle)($user, [
            'article' => [
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'post_type' => ArticlePostType::Markdown->value,
                'status' => $validated['status'],
                'contents' => $contents,
            ],
            'should_notify' => false,
        ]);

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
            'markdown' => $schema->string()
                ->required()
                ->description('Markdown形式の記事本文。'),
            'status' => $schema->string()
                ->enum([ArticleStatus::Publish->value, ArticleStatus::Draft->value])
                ->required()
                ->description('公開ステータス。publish（公開）または draft（下書き）。'),
            'thumbnail_id' => $schema->integer()
                ->nullable()
                ->description('サムネイル画像のattachment ID（任意）。user-my-article-showのattachmentsから取得したIDを指定します。'),
        ];
    }
}
