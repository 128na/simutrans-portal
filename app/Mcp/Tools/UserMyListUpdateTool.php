<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

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

class UserMyListUpdateTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        ログイン中のユーザーのマイリストを更新します。

        ## レスポンス
        更新されたマイリスト情報を返します。
        - id, title, note, is_public, slug, items_count, created_at, updated_at
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
            'title' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        try {
            $mylist = MyList::where('id', $validated['mylist_id'])
                ->where('user_id', $user->id)
                ->withCount('items')
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return Response::error('MyList not found.');
        }

        $updated = $this->service->updateList(
            $mylist,
            $validated['title'],
            $validated['note'] ?? null,
            (bool) ($validated['is_public'] ?? false),
        );

        $updated->loadCount('items');
        $httpRequest = app(HttpRequest::class);

        return Response::json(
            (new MyListShowResource($updated))->response($httpRequest)->getData(true)
        );
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
                ->description('更新するマイリストID。'),
            'title' => $schema->string()
                ->min(1)
                ->max(255)
                ->required()
                ->description('マイリストのタイトル。'),
            'note' => $schema->string()
                ->max(2000)
                ->nullable()
                ->description('マイリストのメモ（任意）。'),
            'is_public' => $schema->boolean()
                ->nullable()
                ->description('true にすると公開マイリストとなります。'),
        ];
    }
}
