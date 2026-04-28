<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\MyList;
use App\Models\User;
use App\Services\MyListService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UserMyListDeleteTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        ログイン中のユーザーのマイリストを削除します。
        マイリスト内のアイテムもすべて削除されます。

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
        ]);

        try {
            $mylist = MyList::where('id', $validated['mylist_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return Response::error('MyList not found.');
        }

        $this->service->deleteList($mylist);

        return Response::json(['message' => 'Deleted successfully.']);
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
                ->description('削除するマイリストID。'),
        ];
    }
}
