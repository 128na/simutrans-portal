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

class UserMyListCreateTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        ログイン中のユーザーのマイリストを作成します。

        ## レスポンス
        作成されたマイリスト情報を返します。
        - id: マイリストID
        - title: タイトル
        - note: メモ
        - is_public: 公開フラグ
        - slug: 公開用スラグ（公開時にURLで共有できます）
        - items_count: アイテム数（作成直後は0）
        - created_at, updated_at
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
            'title' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $list = $this->service->createList(
            $user,
            $validated['title'],
            $validated['note'] ?? null,
            (bool) ($validated['is_public'] ?? false),
        );

        $list->loadCount('items');
        $httpRequest = app(HttpRequest::class);

        return Response::json(
            (new MyListShowResource($list))->response($httpRequest)->getData(true)
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
                ->default(false)
                ->description('true にすると公開マイリストとなりURLで共有できます。'),
        ];
    }
}
