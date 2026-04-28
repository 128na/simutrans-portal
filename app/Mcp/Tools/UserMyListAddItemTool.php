<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Http\Resources\Mypage\MyListItem as MyListItemResource;
use App\Models\Article;
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

class UserMyListAddItemTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        マイリストに公開記事を追加します。
        追加できるのは公開済み記事のみです。既に追加済みの記事は追加できません。

        ## レスポンス
        追加されたアイテム情報を返します。
        - id: アイテムID
        - note: メモ
        - position: 並び順（末尾に追加されます）
        - created_at
        - article: 記事情報
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
            'article_id' => ['required', 'integer'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $mylist = MyList::where('id', $validated['mylist_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return Response::error('MyList not found.');
        }

        try {
            $article = Article::where('id', $validated['article_id'])->firstOrFail();
        } catch (ModelNotFoundException) {
            return Response::error('Article not found.');
        }

        try {
            $item = $this->service->addItemToList($mylist, $article, $validated['note'] ?? null);
        } catch (\InvalidArgumentException $e) {
            return Response::error($e->getMessage());
        }

        $httpRequest = app(HttpRequest::class);

        return Response::json(
            (new MyListItemResource($item->load('article.user.profile')))->response($httpRequest)->getData(true)
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
                ->description('アイテムを追加するマイリストID。'),
            'article_id' => $schema->integer()
                ->required()
                ->description('追加する記事ID（公開済み記事のみ）。'),
            'note' => $schema->string()
                ->max(2000)
                ->nullable()
                ->description('アイテムへのメモ（任意）。'),
        ];
    }
}
