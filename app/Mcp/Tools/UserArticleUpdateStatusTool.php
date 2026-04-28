<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Enums\ArticleStatus;
use App\Events\Article\ArticleUpdated;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Models\User;
use App\Repositories\ArticleRepository;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\JsonSchema\Types\Type;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class UserArticleUpdateStatusTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        ログイン中のユーザーの記事のステータスを変更します。
        タイトルや本文は変更しません。ステータスのみを更新します。

        ## ステータス値
        - publish: 公開（初回公開時は公開日時が設定されます）
        - draft: 下書き
        - private: 非公開
        - trash: ゴミ箱

        ## レスポンス
        - id: 記事ID
        - title: タイトル
        - slug: スラッグ
        - status: 更新後のステータス
        - post_type: 投稿形式
        - published_at: 公開日時
        - modified_at: 更新日時
    MARKDOWN;

    public function __construct(private readonly ArticleRepository $repository) {}

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
            'article_id' => ['required', 'integer'],
            'status' => ['required', 'string', Rule::in($this->allowedStatusValues())],
        ]);

        try {
            $article = Article::where('id', $validated['article_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return Response::error('Article not found.');
        }

        $newStatus = ArticleStatus::from($validated['status']);
        $notYetPublished = is_null($article->published_at);

        $updateData = [
            'status' => $newStatus,
            'modified_at' => CarbonImmutable::now()->toDateTimeString(),
        ];

        if ($notYetPublished && $newStatus === ArticleStatus::Publish) {
            $updateData['published_at'] = CarbonImmutable::now()->toDateTimeString();
        }

        $this->repository->update($article, $updateData);
        $article->refresh();

        dispatch(new JobUpdateRelated($article->id));
        event(new ArticleUpdated($article, false, false));

        return Response::json([
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'status' => $article->status->value,
            'post_type' => $article->post_type->value,
            'published_at' => $article->published_at?->format('Y/m/d H:i'),
            'modified_at' => $article->modified_at?->format('Y/m/d H:i'),
        ]);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'article_id' => $schema->integer()
                ->required()
                ->description('ステータスを変更する記事ID。user-my-article-listで取得したidを指定します。'),
            'status' => $schema->string()
                ->enum($this->allowedStatusValues())
                ->required()
                ->description('新しいステータス。publish / draft / private / trash のいずれか。'),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function allowedStatusValues(): array
    {
        return [
            ArticleStatus::Publish->value,
            ArticleStatus::Draft->value,
            ArticleStatus::Private->value,
            ArticleStatus::Trash->value,
        ];
    }
}
