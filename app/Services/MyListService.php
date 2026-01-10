<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Article;
use App\Models\MyList;
use App\Models\MyListItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;

class MyListService
{
    /**
     * ユーザーのマイリスト一覧取得（ページネーション付き）
     *
     * @return Paginator<int, MyList>
     */
    public function getListsForUser(User $user, int $page = 1, int $perPage = 20, string $sort = 'updated_at:desc'): Paginator
    {
        $query = MyList::whereBelongsToUser($user);

        [$sortField, $sortDirection] = $this->parseSortParam($sort);
        $query->orderBy($sortField, $sortDirection);

        return $query->simplePaginate($perPage, ['*'], 'page', $page);
    }

    /**
     * リストを作成
     */
    public function createList(User $user, string $title, ?string $note = null, bool $isPublic = false): MyList
    {
        $list = MyList::create([
            'user_id' => $user->id,
            'title' => $title,
            'note' => $note,
            'is_public' => $isPublic,
        ]);

        // 公開化する場合は slug を生成
        if ($isPublic) {
            $list->slug = $this->generateSlug($list->id);
            $list->save();
        }

        return $list;
    }

    /**
     * リストを更新
     */
    public function updateList(MyList $list, string $title, ?string $note = null, bool $isPublic = false): MyList
    {
        $list->fill([
            'title' => $title,
            'note' => $note,
            'is_public' => $isPublic,
        ]);

        // 公開化する場合は slug を生成（既になければ）
        if ($isPublic && ! $list->slug) {
            $list->slug = $this->generateSlug($list->id);
        } elseif (! $isPublic) {
            // 非公開化する場合は slug をクリア
            $list->slug = null;
        }

        $list->save();

        return $list;
    }

    /**
     * リストを削除
     */
    public function deleteList(MyList $list): bool
    {
        return (bool) $list->delete();
    }

    /**
     * リストのアイテム一覧取得（ページネーション付き）
     * 非公開記事は除外したビューを返す（所有者向け）
     *
     * @return Paginator<int, MyListItem>
     */
    public function getItemsForList(MyList $list, int $page = 1, int $perPage = 20, string $sort = 'position'): Paginator
    {
        $query = $list->items()
            ->with(['article', 'article.user'])
            ->whereHas('article', function ($q) {
                $this->applyPublicArticleFilter($q);
            });

        [$sortField, $sortDirection] = $this->parseSortParam($sort);

        if ($sortField === 'position') {
            $query->orderBy('position', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        return $query->simplePaginate($perPage, ['*'], 'page', $page);
    }

    /**
     * リストのアイテム一覧取得（公開用 - 非公開記事を除外）
     *
     * @return Paginator<int, MyListItem>
     */
    public function getPublicItemsForList(MyList $list, int $page = 1, int $perPage = 20, string $sort = 'position'): Paginator
    {
        return $this->getItemsForList($list, $page, $perPage, $sort);
    }

    /**
     * アイテムを追加
     */
    public function addItemToList(MyList $list, Article $article, ?string $note = null): MyListItem
    {
        // 公開記事のみ追加可能
        if (! $this->isArticlePublic($article)) {
            throw new \InvalidArgumentException('Only published articles can be added to the list.');
        }

        // 位置は末尾に設定
        $maxPosition = (int) ($list->items()->max('position') ?? 0);
        $position = $maxPosition + 1;

        return MyListItem::create([
            'list_id' => $list->id,
            'article_id' => $article->id,
            'note' => $note,
            'position' => $position,
        ]);
    }

    /**
     * アイテムを更新（メモと位置）
     */
    public function updateItem(MyListItem $item, ?string $note = null, ?int $position = null): MyListItem
    {
        if ($note !== null) {
            $item->note = $note;
        }
        if ($position !== null) {
            $item->position = $position;
        }
        $item->save();

        return $item;
    }

    /**
     * アイテムを削除（冪等）
     */
    public function removeItem(MyListItem $item): bool
    {
        return (bool) $item->delete();
    }

    /**
     * アイテムを並び替え（バルク更新）
     *
     * @param  array<int, array<string, int|string>>  $itemPositions
     */
    public function reorderItems(MyList $list, array $itemPositions): void
    {
        // $itemPositions は [['id' => 1, 'position' => 1], ...] 形式を想定
        foreach ($itemPositions as $item) {
            MyListItem::where('id', $item['id'])
                ->where('list_id', $list->id)
                ->update(['position' => $item['position']]);
        }
    }

    /**
     * 公開リストを slug で取得
     */
    public function getPublicListBySlug(string $slug): ?MyList
    {
        return MyList::wherePublic()->where('slug', $slug)->first();
    }

    /**
     * 記事が公開かどうかを判定
     */
    public function isArticlePublic(Article $article): bool
    {
        // Check status
        if (! isset($article->status) || $article->status->value !== 'published') {
            return false;
        }

        // Check if article is soft-deleted
        if ($article->trashed()) {
            return false;
        }

        // Check if author is soft-deleted
        $user = $article->user ?? null;
        if ($user !== null && $user->trashed()) {
            return false;
        }

        return true;
    }

    /**
     * ソートパラメータをパース（"field:direction" 形式）
     *
     * @return array<int, string>
     */
    private function parseSortParam(string $sort): array
    {
        if (str_contains($sort, ':')) {
            [$field, $direction] = explode(':', $sort);

            return [trim($field), strtoupper(trim($direction)) === 'DESC' ? 'desc' : 'asc'];
        }

        return [$sort, 'asc'];
    }

    /**
     * slug を生成（UUID ベース）
     */
    private function generateSlug(int $listId): string
    {
        return Str::slug(Str::random(10)).'-'.$listId;
    }

    /**
     * 公開記事フィルタを適用
     *
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    private function applyPublicArticleFilter(Builder $query): Builder
    {
        return $query->whereNotNull('status')
            ->where('status', 'published')
            ->whereNull('deleted_at');
    }
}
