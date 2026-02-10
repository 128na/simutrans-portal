<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\FrontMyList\PublicShowAction;
use App\Http\Resources\Mypage\MyListItem as MyListItemResource;
use App\Http\Resources\Mypage\MyListShow as MyListShowResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request as HttpRequest;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GuestPublicMyListShowTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        未ログインで公開マイリストの詳細を取得します。

        ## レスポンス
        - data: マイリストアイテム一覧
          - id: アイテムID
          - note: アイテムメモ
          - position: 並び順
          - created_at: 追加日時
          - article: 記事情報
            - id: 記事ID
            - title: 記事タイトル
            - published_at: 公開日時 (公開記事のみ)
            - thumbnail: サムネイルURL (公開記事のみ)
            - url: 記事URL (公開記事のみ)
            - download_url: ダウンロードURL (公開記事かつアドオン投稿のみ)
            - addon_page_url: 掲載ページURL (公開記事かつアドオン紹介のみ)
            - user: 投稿者情報 (公開記事のみ)
              - name: 投稿者名
              - avatar: 投稿者アバターURL
        - list: マイリスト情報
          - id: マイリストID
          - title: タイトル
          - note: メモ
          - is_public: 公開フラグ
          - slug: 公開用スラグ
          - items_count: アイテム数
          - created_at: 作成日時
          - updated_at: 更新日時
        - links: ページネーションリンク
        - meta: ページネーション情報
    MARKDOWN;

    public function __construct(private PublicShowAction $publicShowAction) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort' => ['nullable', 'string', 'max:50'],
        ]);

        $page = (int) ($validated['page'] ?? 1);
        $perPage = (int) ($validated['per_page'] ?? 20);
        $sort = (string) ($validated['sort'] ?? 'position');

        try {
            $result = ($this->publicShowAction)($validated['slug'], $page, $perPage, $sort);
        } catch (ModelNotFoundException) {
            return Response::error('MyList not found.');
        }

        $httpRequest = app(HttpRequest::class);
        $payload = MyListItemResource::collection($result['items'])
            ->additional([
                'list' => new MyListShowResource($result['list']),
            ])
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
            'slug' => $schema->string()
                ->min(1)
                ->max(255)
                ->required()
                ->description('公開マイリストのslug。'),
            'page' => $schema->integer()
                ->min(1)
                ->description('ページ番号。')
                ->nullable(),
            'per_page' => $schema->integer()
                ->min(1)
                ->max(100)
                ->description('1ページあたりの件数。')
                ->nullable(),
            'sort' => $schema->string()
                ->description('ソート指定（field:direction）。例: position:asc')
                ->nullable(),
        ];
    }
}
