<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Article;
use App\Models\User;
use App\Repositories\Article\MypageArticleRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class UserAnalyticsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        ログイン中のユーザーの公開記事のアクセス解析（累計閲覧数・累計ダウンロード数）を返します。
        未公開記事や下書きは含まれません。

        ## レスポンス（配列）
        - id: 記事ID
        - title: タイトル
        - published_at: 公開日
        - total_view_count: 累計閲覧数
        - total_conversion_count: 累計ダウンロード数
    MARKDOWN;

    public function __construct(private readonly MypageArticleRepository $repository) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return Response::error('Unauthorized.');
        }

        $articles = $this->repository->getForAnalyticsList($user);

        return Response::json(
            $articles->map(fn (Article $article): array => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'published_at' => $article->published_at?->format('Y/m/d'),
                'total_view_count' => $article->totalViewCount->count ?? 0,
                'total_conversion_count' => $article->totalConversionCount->count ?? 0,
            ])->values()->all()
        );
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
