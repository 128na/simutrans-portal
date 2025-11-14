<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ArticleAnalyticsType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Attachment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\LazyCollection;

/**
 * @extends BaseRepository<Article>
 */
final class ArticleRepository extends BaseRepository
{
    public const array MYPAGE_RELATIONS = ['user', 'attachments.fileInfo', 'categories', 'tags', 'totalViewCount', 'totalConversionCount', 'articles'];

    public const array FRONT_RELATIONS = ['user.profile', 'attachments.fileInfo', 'categories', 'tags', 'articles', 'relatedArticles'];

    public const array SHOW_RELATIONS = ['user.profile', 'attachments.fileInfo', 'categories', 'tags', 'articles', 'relatedArticles'];

    public const int PER_PAGE_SIMPLE = 6;

    public function __construct(Article $article)
    {
        parent::__construct($article);
    }

    /**
     * 添付ファイルを関連付ける.
     *
     * @param  array<int|string>  $attachmentsIds
     */
    public function syncAttachments(Article $article, array $attachmentsIds): void
    {
        // add
        $attachments = $article->user->myAttachments()->find($attachmentsIds);
        $article->attachments()->saveMany($attachments);

        // remove
        /** @var Collection<int,Attachment> */
        $shouldDetach = $article->attachments()->whereNotIn('id', $attachmentsIds)->get();
        foreach ($shouldDetach as $attachment) {
            $attachment->attachmentable()->disassociate()->save();
        }
    }

    /**
     * 記事を関連付ける.
     *
     * @param  array<int|string>  $articleIds
     */
    public function syncArticles(Article $article, array $articleIds): void
    {
        $result = $article->articles()->sync($articleIds);
        logger('[ArticleRepository] syncArticles', $result);
    }

    /**
     * カテゴリを関連付ける.
     *
     * @param  array<int|string>  $categoryIds
     */
    public function syncCategories(Article $article, array $categoryIds): void
    {
        $article->categories()->sync($categoryIds);
    }

    /**
     * タグを関連付ける.
     *
     * @param  array<int|string>  $tagIds
     */
    public function syncTags(Article $article, array $tagIds): void
    {
        $article->tags()->sync($tagIds);
    }

    /**
     * アナリティクス用のデータ取得.
     *
     * @param  array<int|string>  $ids
     * @param  array<string>  $period
     * @return Collection<int, Article>
     */
    public function findAllForAnalytics(User $user, array $ids, ArticleAnalyticsType $articleAnalyticsType, array $period): Collection
    {
        $periodQuery = function ($query) use ($articleAnalyticsType, $period): void {
            $query->select('article_id', 'count', 'period')
                ->where('type', $articleAnalyticsType)->whereBetween('period', $period);
        };

        /** @var Collection<int, Article> */
        return $this->model
            ->query()
            ->select('id')
            ->where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->with([
                'viewCounts' => $periodQuery,
                'conversionCounts' => $periodQuery,
            ])
            ->orderBy('published_at', 'desc')
            ->get();
    }

    /**
     * ユーザーに紐づくデータを返す.
     *
     * @param  array<int,string>  $relations
     * @return Collection<int, Article>
     */
    public function findAllByUser(User $user, array $relations = self::FRONT_RELATIONS, string $order = 'modified_at'): Collection
    {
        /** @var Collection<int, Article> */
        return $this->model
            ->query()
            ->select(['articles.*'])
            ->where('user_id', $user->id)
            ->with($relations)
            ->orderBy($order, 'desc')
            ->get();
    }

    /**
     * リンク切れチェック対象の記事.
     *
     * @return LazyCollection<int,Article>
     */
    public function cursorCheckLink(): LazyCollection
    {
        return $this->model
            ->active()
            ->linkCheckTarget()
            ->select('id', 'user_id', 'title', 'slug', 'post_type', 'contents')
            ->with('user:id,email')
            ->cursor();
    }

    /**
     * 論理削除されているものも含めた一覧.
     *
     * @return Collection<int, Article>
     */
    public function findAllWithTrashed(): Collection
    {
        return $this->model
            ->withTrashed()
            ->withUserTrashed()
            ->with(['user' => fn($q) => $q->withTrashed()])
            ->get();
    }

    /**
     * 論理削除されているものも含めて探す.
     */
    public function findOrFailWithTrashed(int $id): Article
    {
        return $this->model
            ->withTrashed()
            ->withUserTrashed()
            ->findOrFail($id);
    }

    /**
     * 論理削除状態を切り替える.
     */
    public function toggleDelete(Article $article): void
    {
        $article->trashed()
            ? $article->restore()
            : $article->delete();
    }

    /**
     * 指定時刻を過ぎた予約記事
     *
     * @return LazyCollection<int,Article>
     */
    public function cursorReservations(CarbonImmutable $date): LazyCollection
    {
        return $this->model
            ->where('status', ArticleStatus::Reservation)
            ->where('published_at', '<=', $date)
            ->cursor();
    }
}
