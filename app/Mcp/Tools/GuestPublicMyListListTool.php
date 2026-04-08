<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Actions\FrontMyList\PublicListAction;
use App\Http\Resources\Mypage\MyListShow as MyListShowResource;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Http\Request as HttpRequest;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class GuestPublicMyListListTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        未ログインで公開マイリスト一覧を取得します。

        ## レスポンス
        - data: 公開マイリスト一覧
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

    public function __construct(private PublicListAction $publicListAction) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort' => ['nullable', 'string', 'max:50'],
        ]);

        $page = (int) ($validated['page'] ?? 1);
        $perPage = (int) ($validated['per_page'] ?? 20);
        $sort = (string) ($validated['sort'] ?? 'updated_at:desc');

        $paginator = ($this->publicListAction)($page, $perPage, $sort);
        $httpRequest = app(HttpRequest::class);
        $payload = MyListShowResource::collection($paginator)
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
                ->description('ソート指定（field:direction）。例: updated_at:desc')
                ->nullable(),
        ];
    }
}
