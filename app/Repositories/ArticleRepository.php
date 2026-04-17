<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Attachment;
use App\Repositories\Concerns\ArticleQueryConcern;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\LazyCollection;

class ArticleRepository
{
    use ArticleQueryConcern;

    public function __construct(public Article $model) {}

    /**
     * @param  mixed[]  $data
     */
    public function store(array $data): Article
    {
        return $this->model->create($data);
    }

    /**
     * @param  mixed[]  $data
     */
    public function update(Article $article, array $data): Article
    {
        $article->update($data);

        return $article;
    }

    /**
     * 添付ファイルを関連付ける
     *
     * @param  array<int|string>  $attachmentsIds
     */
    public function syncAttachments(Article $article, array $attachmentsIds): void
    {
        if (! $article->user) {
            return;
        }

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
     * 記事を関連付ける
     *
     * @param  array<int|string>  $articleIds
     */
    public function syncArticles(Article $article, array $articleIds): void
    {
        $result = $article->articles()->sync($articleIds);
        logger('[ArticleRepository] syncArticles', $result);
    }

    /**
     * カテゴリを関連付ける
     *
     * @param  array<int|string>  $categoryIds
     */
    public function syncCategories(Article $article, array $categoryIds): void
    {
        $article->categories()->sync($categoryIds);
    }

    /**
     * タグを関連付ける
     *
     * @param  array<int|string>  $tagIds
     */
    public function syncTags(Article $article, array $tagIds): void
    {
        $article->tags()->sync($tagIds);
    }

    /**
     * リンク切れチェック対象の記事
     *
     * @return LazyCollection<int,Article>
     */
    public function cursorCheckLink(): LazyCollection
    {
        $builder = $this->model
            ->withoutGlobalScopes()
            ->select('articles.id', 'articles.user_id', 'articles.title', 'articles.slug', 'articles.post_type', 'articles.contents')
            ->where('articles.post_type', ArticlePostType::AddonIntroduction->value)
            ->where(
                fn ($query) => $query
                    ->whereNull('articles.contents->exclude_link_check')
                    ->orWhere('articles.contents->exclude_link_check', false)
            );

        $this->joinActiveUsers($builder);
        $this->wherePublished($builder);

        return $builder
            ->with('user:id,email')
            ->cursor();
    }

    /**
     * 指定時刻を過ぎた予約記事
     *
     * @return LazyCollection<int,Article>
     */
    public function cursorReservations(CarbonImmutable $date): LazyCollection
    {
        $builder = $this->model
            ->select('articles.*')
            ->withoutGlobalScopes()
            ->whereNull('articles.deleted_at')
            ->where('articles.status', ArticleStatus::Reservation->value)
            ->where('articles.published_at', '<=', $date);

        $this->joinActiveUsers($builder);

        return $builder->cursor();
    }
}
