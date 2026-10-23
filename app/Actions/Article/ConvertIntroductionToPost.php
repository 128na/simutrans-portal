<?php

declare(strict_types=1);

namespace App\Actions\Article;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Events\Article\ArticleUpdated;
use App\Jobs\Article\JobUpdateRelated;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\Contents\AddonIntroductionContent;
use App\Models\Contents\AddonPostContent;
use App\Repositories\ArticleLinkCheckHistoryRepository;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConvertIntroductionToPost
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private ArticleLinkCheckHistoryRepository $historyRepository,
    ) {}

    public function __invoke(Article $article, int $userId, int $fileId): Article
    {
        return DB::transaction(function () use ($article, $userId, $fileId): Article {
            $article = Article::query()->lockForUpdate()->findOrFail($article->id);
            $this->ensureConvertible($article, $userId);
            $attachment = Attachment::query()->lockForUpdate()->find($fileId);
            if (! $attachment || ! $this->isAvailable($attachment, $article)) {
                throw ValidationException::withMessages(['file' => '利用できる配布ファイルを選択してください。']);
            }

            /** @var AddonIntroductionContent $contents */
            $contents = $article->contents;
            if ($contents->thumbnail !== null) {
                $thumbnail = Attachment::query()->lockForUpdate()->find($contents->thumbnail);
                if (! $thumbnail || $thumbnail->user_id !== $userId
                    || ($thumbnail->attachmentable_id !== null && ($thumbnail->attachmentable_id !== $article->id || $thumbnail->attachmentable_type !== $article->getMorphClass()))) {
                    throw ValidationException::withMessages(['file' => 'サムネイルの関連付けを記事編集画面で確認してください。']);
                }
            }
            $postContents = new AddonPostContent([
                'description' => $contents->description,
                'author' => $contents->author,
                'license' => $contents->license,
                'thanks' => $contents->thanks,
                'thumbnail' => $contents->thumbnail,
                'file' => $attachment->id,
            ]);
            $this->articleRepository->update($article, [
                'post_type' => ArticlePostType::AddonPost,
                'contents' => $postContents,
                'modified_at' => now(),
            ]);
            $article = $article->fresh() ?? $article;
            $this->articleRepository->syncAttachments($article, array_values(array_filter([$postContents->thumbnail, $attachment->id])));
            $this->historyRepository->clear($article);
            DB::afterCommit(function () use ($article): void {
                dispatch(new JobUpdateRelated($article->id));
                event(new ArticleUpdated($article, false, false));
            });

            return $article;
        });
    }

    public function ensureConvertible(Article $article, int $userId): void
    {
        abort_unless($article->user_id === $userId, 403);
        abort_if($article->post_type !== ArticlePostType::AddonIntroduction || $article->status === ArticleStatus::Trash || $article->trashed(), 409, 'この記事は投稿記事へ変換できません。');
    }

    public function isAvailable(Attachment $attachment, Article $article): bool
    {
        return $attachment->user_id === $article->user_id
            && (($attachment->attachmentable_id === null && $attachment->attachmentable_type === null)
                || ($attachment->attachmentable_id === $article->id && $attachment->attachmentable_type === $article->getMorphClass()))
            && $attachment->getPublicDisk()->exists($attachment->path)
            && $attachment->type === 'file';
    }
}
