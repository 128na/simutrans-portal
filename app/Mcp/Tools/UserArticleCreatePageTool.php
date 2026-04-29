<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\Article\StoreArticle;
use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UserArticleCreatePageTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        ページ形式の記事を新規作成します。

        ## カテゴリ
        categories には page 型のカテゴリIDを指定してください。
        利用可能なカテゴリIDは guest-article-search-options-tool で確認できます（type が "page" のもの）。

        ## セクション
        sections は記事本文をブロック単位で構成する配列です（最低1件必要）。
        各セクションのtypeに応じた必須フィールドを指定してください：
        - `{"type": "caption", "caption": "見出しテキスト（max:255）"}`
        - `{"type": "text",    "text":    "本文テキスト（max:2048）"}`
        - `{"type": "url",     "url":     "https://..."}`
        - `{"type": "image",   "id":      123}` （user-attachment-list で取得した画像ID）

        ## パラメータ
        - title: 記事タイトル
        - slug: URLスラッグ（英数字・ハイフン・アンダースコアのみ、自分の記事内で一意）
        - status: publish（公開）または draft（下書き）
        - categories: page型カテゴリIDの配列
        - sections: セクション配列（最低1件）
        - thumbnail_id: サムネイル画像のattachment ID（任意）

        ## レスポンス
        - id: 作成された記事ID
        - title: タイトル
        - slug: スラッグ
        - status: ステータス
        - post_type: 投稿形式（page固定）
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
            'categories' => ['present', 'array', 'min:1'],
            'categories.*' => ['integer'],
            'sections' => ['required', 'array', 'min:1'],
            'sections.*.type' => ['required', 'string', 'in:caption,text,url,image'],
            'sections.*.caption' => ['required_if:sections.*.type,caption', 'nullable', 'string', 'max:255'],
            'sections.*.text' => ['required_if:sections.*.type,text', 'nullable', 'string', 'max:2048'],
            'sections.*.url' => ['required_if:sections.*.type,url', 'nullable', 'url', 'max:255'],
            'sections.*.id' => ['required_if:sections.*.type,image', 'nullable', 'integer'],
            'thumbnail_id' => ['nullable', 'integer'],
        ]);

        $categoryIds = $validated['categories'];
        $invalidCategories = array_filter(
            $categoryIds,
            fn (int $id): bool => ! Category::where('id', $id)->where('type', CategoryType::Page)->exists()
        );

        if (! empty($invalidCategories)) {
            return Response::error('Invalid categories: must be page type. Use guest-article-search-options-tool to find valid category IDs.');
        }

        $contents = ['sections' => $validated['sections']];
        if (isset($validated['thumbnail_id'])) {
            $contents['thumbnail'] = $validated['thumbnail_id'];
        }

        $article = DB::transaction(fn (): Article => ($this->storeArticle)($user, [
            'article' => [
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'post_type' => ArticlePostType::Page->value,
                'status' => $validated['status'],
                'contents' => $contents,
                'categories' => $categoryIds,
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
                ->description('page型カテゴリIDの配列。guest-article-search-options-toolでtype="page"のカテゴリIDを確認してください。'),
            'sections' => $schema->array()
                ->items($schema->object())
                ->required()
                ->description('セクション配列。各要素は {"type": "caption"|"text"|"url"|"image", ...} の形式で指定します。'),
            'thumbnail_id' => $schema->integer()
                ->nullable()
                ->description('サムネイル画像のattachment ID（任意）。user-attachment-listで取得した画像IDを指定します。'),
        ];
    }
}
