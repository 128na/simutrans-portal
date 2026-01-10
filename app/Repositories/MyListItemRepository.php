<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\MyList;
use App\Models\MyListItem;
use Illuminate\Contracts\Pagination\Paginator;

class MyListItemRepository
{
    public function __construct(
        private readonly MyListItem $model,
    ) {}

    /**
     * リストのアイテム一覧取得（ページネーション付き）
     *
     * @return Paginator<int, MyListItem>
     */
    public function paginateForList(MyList $list, int $page = 1, int $perPage = 20, string $sortField = 'position', string $sortDirection = 'asc'): Paginator
    {
        $query = $list->items()
            ->with([
                'article.user.profile.attachments',
                'article.attachments',
            ])
            ->orderBy($sortField, $sortDirection);

        /** @var Paginator<int, MyListItem> $result */
        $result = $query->simplePaginate($perPage, ['*'], 'page', $page);

        return $result;
    }

    /**
     * リストの公開アイテム一覧取得（ページネーション付き）
     * 非公開記事を除外
     *
     * @return Paginator<int, MyListItem>
     */
    public function paginatePublicForList(MyList $list, int $page = 1, int $perPage = 20, string $sortField = 'position', string $sortDirection = 'asc'): Paginator
    {
        $query = $list->items()
            ->with([
                'article.user.profile.attachments',
                'article.attachments',
            ])
            ->whereHas('article', function ($q) {
                $q->where('status', \App\Enums\ArticleStatus::Publish)
                    ->whereNull('deleted_at');
            })
            ->orderBy($sortField, $sortDirection);

        /** @var Paginator<int, MyListItem> $result */
        $result = $query->simplePaginate($perPage, ['*'], 'page', $page);

        return $result;
    }

    /**
     * アイテムを ID で取得
     */
    public function findById(int $id): ?MyListItem
    {
        return $this->model->find($id);
    }

    /**
     * アイテムを ID で取得（存在しない場合は例外）
     */
    public function findByIdOrFail(int $id): MyListItem
    {
        return $this->model->findOrFail($id);
    }

    /**
     * リストと記事で既存アイテムを取得
     */
    public function findByListAndArticle(int $listId, int $articleId): ?MyListItem
    {
        return $this->model
            ->where('list_id', $listId)
            ->where('article_id', $articleId)
            ->first();
    }

    /**
     * アイテムを作成
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): MyListItem
    {
        return $this->model->create($data);
    }

    /**
     * アイテムを更新
     *
     * @param  array<string, mixed>  $data
     */
    public function update(MyListItem $item, array $data): void
    {
        $item->update($data);
    }

    /**
     * アイテムを削除
     */
    public function delete(MyListItem $item): void
    {
        $item->delete();
    }

    /**
     * 複数アイテムの位置を更新（バルク更新）
     *
     * @param  array<int, array<string, int|string>>  $itemPositions
     */
    public function updatePositions(MyList $list, array $itemPositions): void
    {
        foreach ($itemPositions as $item) {
            $this->model
                ->where('id', $item['id'])
                ->where('list_id', $list->id)
                ->update(['position' => $item['position']]);
        }
    }
}
