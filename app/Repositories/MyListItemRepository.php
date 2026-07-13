<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ArticleStatus;
use App\Models\MyList;
use App\Models\MyListItem;
use App\Repositories\Concerns\HasCrud;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class MyListItemRepository
{
    use HasCrud;

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
            ->whereHas('article', function ($q): void {
                $q->where('status', ArticleStatus::Publish)
                    ->whereNull('deleted_at');
            })
            ->orderBy($sortField, $sortDirection);

        /** @var Paginator<int, MyListItem> $result */
        $result = $query->simplePaginate($perPage, ['*'], 'page', $page);

        return $result;
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
     * 複数アイテムの位置を更新（バルク更新）
     *
     * id ごとに個別UPDATEを発行せず、CASE WHEN による単一クエリで更新する。
     * list_id での絞り込みは維持し、他リストのアイテムを誤って更新しないようにする。
     *
     * @param  array<int, array<string, int|string>>  $itemPositions
     */
    public function updatePositions(MyList $list, array $itemPositions): void
    {
        if ($itemPositions === []) {
            return;
        }

        $ids = array_column($itemPositions, 'id');

        $caseSql = 'CASE id';
        $bindings = [];
        foreach ($itemPositions as $item) {
            $caseSql .= ' WHEN ? THEN ?';
            $bindings[] = $item['id'];
            $bindings[] = $item['position'];
        }
        $caseSql .= ' ELSE position END';

        $table = $this->model->getTable();
        $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));

        $bindings[] = $list->id;
        array_push($bindings, ...$ids);

        DB::statement(
            "UPDATE {$table} SET position = {$caseSql} WHERE list_id = ? AND id IN ({$idPlaceholders})",
            $bindings
        );
    }
}
