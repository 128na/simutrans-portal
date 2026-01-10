<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Article;
use App\Models\MyList;
use App\Models\MyListItem;
use App\Models\User;
use App\Repositories\MyListItemRepository;
use App\Repositories\MyListRepository;
use Illuminate\Support\Str;

class MyListService
{
    public function __construct(
        private readonly MyListRepository $listRepository,
        private readonly MyListItemRepository $itemRepository,
    ) {}

    /**
     * ユーザーのマイリスト一覧取得（ページネーション付き）
     *
     * @return \Illuminate\Contracts\Pagination\Paginator<int, MyList>
     */
    public function getListsForUser(User $user, int $page = 1, int $perPage = 20, string $sort = 'updated_at:desc')
    {
        [$sortField, $sortDirection] = $this->parseSortParam($sort);

        return $this->listRepository->paginateForUser($user, $page, $perPage, $sortField, $sortDirection);
    }

    /**
     * リストを作成
     */
    public function createList(User $user, string $title, ?string $note = null, bool $isPublic = false): MyList
    {
        $data = [
            'user_id' => $user->id,
            'title' => $title,
            'note' => $note,
            'is_public' => $isPublic,
            'slug' => Str::uuid()->toString(),
        ];

        // 新規作成時は常に slug を生成（URLの一貫性を保つ）
        $list = $this->listRepository->create($data);
        $this->listRepository->update($list, []);

        return $list;
    }

    /**
     * リストを更新
     */
    public function updateList(MyList $list, string $title, ?string $note = null, bool $isPublic = false): MyList
    {
        $data = [
            'title' => $title,
            'note' => $note,
            'is_public' => $isPublic,
        ];

        $this->listRepository->update($list, $data);
        $list->refresh();

        return $list;
    }

    /**
     * リストを削除
     */
    public function deleteList(MyList $list): void
    {
        $this->listRepository->delete($list);
    }

    /**
     * リストのアイテム一覧取得（ページネーション付き）
     * 所有者向け: 非公開記事も含めて返す
     *
     * @return \Illuminate\Contracts\Pagination\Paginator<int, MyListItem>
     */
    public function getItemsForList(MyList $list, int $page = 1, int $perPage = 20, string $sort = 'position')
    {
        [$sortField, $sortDirection] = $this->parseSortParam($sort);

        $paginator = $this->itemRepository->paginateForList($list, $page, $perPage, $sortField, $sortDirection);

        return $paginator;
    }

    /**
     * リストのアイテム一覧取得（公開用 - 非公開記事を除外）
     *
     * @return \Illuminate\Contracts\Pagination\Paginator<int, MyListItem>
     */
    public function getPublicItemsForList(MyList $list, int $page = 1, int $perPage = 20, string $sort = 'position')
    {
        [$sortField, $sortDirection] = $this->parseSortParam($sort);

        return $this->itemRepository->paginatePublicForList($list, $page, $perPage, $sortField, $sortDirection);
    }

    /**
     * アイテムを追加
     */
    public function addItemToList(MyList $list, Article $article, ?string $note = null): MyListItem
    {
        // 公開記事のみ追加可能
        if (! $this->isArticlePublic($article)) {
            throw new \InvalidArgumentException(__('validation.custom.mylist_not_public_article'));
        }

        // 既に追加済みのアイテムは追加不可
        $existingItem = $this->itemRepository->findByListAndArticle($list->id, $article->id);
        if ($existingItem !== null) {
            throw new \InvalidArgumentException(__('validation.custom.mylist_article_already_exists'));
        }

        // 位置は末尾に設定
        $maxPosition = (int) ($list->items()->max('position') ?? 0);
        $position = $maxPosition + 1;

        return $this->itemRepository->create([
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
        $data = [];
        if ($note !== null) {
            $data['note'] = $note;
        }
        if ($position !== null) {
            $data['position'] = $position;
        }

        if (! empty($data)) {
            $this->itemRepository->update($item, $data);
            $item->refresh();
        }

        return $item;
    }

    /**
     * アイテムを削除（冪等）
     */
    public function removeItem(MyListItem $item): void
    {
        $this->itemRepository->delete($item);
    }

    /**
     * アイテムを並び替え（バルク更新）
     *
     * @param  array<int, array<string, int|string>>  $itemPositions
     */
    public function reorderItems(MyList $list, array $itemPositions): void
    {
        $this->itemRepository->updatePositions($list, $itemPositions);
    }

    /**
     * 公開リストを slug で取得
     */
    public function getPublicListBySlug(string $slug): MyList
    {
        $mylist = $this->listRepository->findOrFailPublicBySlug($slug);
        $mylist->load('user');

        return $mylist;
    }

    /**
     * 記事が公開かどうかを判定
     */
    public function isArticlePublic(Article $article): bool
    {
        // Check status
        if (! isset($article->status) || $article->status !== \App\Enums\ArticleStatus::Publish) {
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
}
