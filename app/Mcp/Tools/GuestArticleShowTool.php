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

                ## 共通項目
                - id: 記事ID
                - title: 記事タイトル
                - slug: 記事slug
                - post_type: 投稿形式 (addon-post|addon-introduction|page|markdown)
                - user: 投稿者情報
                - categories: カテゴリ一覧
                - tags: タグ一覧
                - articles: 関連記事一覧
                - relatedArticles: 関連記事一覧 (相互)
                - attachments: 添付ファイル一覧
                - published_at: 公開日時
                - modified_at: 最終更新日時
                - download_url: 記事のダウンロードURL。
                - addon_page_url: 掲載先の外部URL。

                ## 投稿形式別のcontents
                - addon-post
                    - description: 本文
                    - file: 添付ファイルID
                    - author: 作者名
                    - license: ライセンス
                    - thanks: 謝辞
                    - thumbnail: サムネイルID
                - addon-introduction
                    - description: 本文
                    - link: 掲載URL
                    - author: 作者名
                    - license: ライセンス
                    - thanks: 謝辞
                    - thumbnail: サムネイルID
                    - agreement: 掲載許可の有無
                    - exclude_link_check: リンクチェック除外
                - page
                    - sections: セクション配列 (type: text|image|caption|url)
                    - thumbnail: サムネイルID
                - markdown
                    - markdown: Markdown本文
                    - thumbnail: サムネイルID
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
