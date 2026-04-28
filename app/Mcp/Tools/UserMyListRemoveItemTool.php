<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\MyList;
use App\Models\MyListItem;
use App\Models\User;
use App\Services\MyListService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UserMyListRemoveItemTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        マイリストからアイテムを削除します。
        アイテムIDは user-my-list-show で取得できます。

        ## レスポンス
        - message: 削除完了メッセージ
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
            'item_id' => ['required', 'integer'],
        ]);

        try {
            $mylist = MyList::where('id', $validated['mylist_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return Response::error('MyList not found.');
        }

        try {
            $item = MyListItem::where('id', $validated['item_id'])
                ->where('list_id', $mylist->id)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return Response::error('Item not found in this MyList.');
        }

        $this->service->removeItem($item);

        return Response::json(['message' => 'Removed successfully.']);
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
                ->description('操作対象のマイリストID。'),
            'item_id' => $schema->integer()
                ->required()
                ->description('削除するアイテムID。user-my-list-showで取得したidを指定します。'),
        ];
    }
}
