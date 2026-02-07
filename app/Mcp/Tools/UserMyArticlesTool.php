<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\User;
use App\Repositories\ArticleRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Auth;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class UserMyArticlesTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        ログインユーザー自身の記事一覧を取得します。全ステータスが対象です。
    MARKDOWN;

    public function __construct(private ArticleRepository $articleRepository) {}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $user = Auth::guard('mcp')->user();
        if (! $user instanceof User) {
            return Response::error('Unauthorized.');
        }

        if (! $user->tokenCan('mcp:use')) {
            return Response::error('Forbidden.');
        }

        $articles = $this->articleRepository->getForMypageList($user);

        return Response::json([
            'articles' => $articles,
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\Types\Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
