<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Http\Resources\Mypage\MyListShow as MyListShowResource;
use App\Models\User;
use App\Services\MyListService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class UserMyListListTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        ログイン中のユーザーのマイリスト一覧を取得します。非公開マイリストも含みます。

        ## レスポンス
        - data: マイリスト一覧
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

    public function __construct(private readonly MyListService $service) {}

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
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort' => ['nullable', 'string', 'max:50'],
        ]);

        $page = (int) ($validated['page'] ?? 1);
        $perPage = (int) ($validated['per_page'] ?? 20);
        $sort = (string) ($validated['sort'] ?? 'updated_at:desc');

        $lists = $this->service->getListsForUser($user, $page, $perPage, $sort);
        $httpRequest = app(HttpRequest::class);

        $payload = MyListShowResource::collection($lists)
            ->response($httpRequest)
            ->getData(true);

        return Response::json($payload);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
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
                ->description('ソート指定（field:direction）。例: updated_at:desc, created_at:asc')
                ->nullable(),
        ];
    }
}
