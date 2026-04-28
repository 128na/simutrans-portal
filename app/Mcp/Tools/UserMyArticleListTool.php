<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\User;
use App\Repositories\Article\MypageArticleRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class UserMyArticleListTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        ログイン中のユーザーが投稿した記事一覧を取得します。下書き・非公開記事も含みます。

        ## レスポンス
        - data: 記事一覧
            - id: 記事ID
            - title: タイトル
            - slug: スラッグ
            - status: ステータス（publish / draft / private / trash）
            - post_type: 投稿形式（addon-post / addon-introduction / page / markdown）
            - published_at: 公開日時
            - modified_at: 更新日時
    MARKDOWN;

    public function __construct(private readonly MypageArticleRepository $articleRepository) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return Response::error('Unauthorized.');
        }

        $articles = $this->articleRepository->getForMypageList($user);

        $data = $articles->map(fn ($article) => [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'status' => $article->status->value,
            'post_type' => $article->post_type->value,
            'published_at' => $article->published_at?->format('Y/m/d H:i'),
            'modified_at' => $article->modified_at?->format('Y/m/d H:i'),
        ])->values()->all();

        return Response::json(['data' => $data]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
