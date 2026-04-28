<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Http\Resources\Mypage\MyListItem as MyListItemResource;
use App\Http\Resources\Mypage\MyListShow as MyListShowResource;
use App\Models\MyList;
use App\Models\User;
use App\Services\MyListService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class UserMyListShowTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        ログイン中のユーザーのマイリスト詳細とアイテム一覧を取得します。
        非公開マイリストも取得可能です。非公開記事は「非公開記事」として表示されます。

        ## レスポンス
        - data: マイリストアイテム一覧
            - id: アイテムID
            - note: アイテムメモ
            - position: 並び順
            - created_at: 追加日時
            - article: 記事情報
                - id: 記事ID
                - title: タイトル（公開記事のみ詳細あり）
                - url: 記事URL（公開記事のみ）
        - list: マイリスト情報
            - id, title, note, is_public, slug, items_count, created_at, updated_at
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
            'mylist_id' => ['required', 'integer'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $mylist = MyList::where('id', $validated['mylist_id'])
                ->where('user_id', $user->id)
                ->withCount('items')
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return Response::error('MyList not found.');
        }

        $page = (int) ($validated['page'] ?? 1);
        $perPage = (int) ($validated['per_page'] ?? 20);
        $sort = (string) ($validated['sort'] ?? 'position');

        $items = $this->service->getItemsForList($mylist, $page, $perPage, $sort);
        $httpRequest = app(HttpRequest::class);

        $payload = MyListItemResource::collection($items)
            ->additional([
                'list' => new MyListShowResource($mylist),
            ])
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
            'mylist_id' => $schema->integer()
                ->required()
                ->description('マイリストID。user-my-list-listで取得したidを指定します。'),
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
