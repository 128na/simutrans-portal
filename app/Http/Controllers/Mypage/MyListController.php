<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyList\ReorderMyListItemsRequest;
use App\Http\Requests\MyList\StoreMyListItemRequest;
use App\Http\Requests\MyList\StoreMyListRequest;
use App\Http\Requests\MyList\UpdateMyListItemRequest;
use App\Http\Requests\MyList\UpdateMyListRequest;
use App\Http\Resources\Mypage\MyListItem as MyListItemResource;
use App\Http\Resources\Mypage\MyListShow as MyListShowResource;
use App\Models\Article;
use App\Models\MyList;
use App\Models\MyListItem;
use App\Services\MyListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MyListController extends Controller
{
    public function __construct(private MyListService $service) {}

    /**
     * 自分のマイリスト一覧取得
     */
    public function index(Request $request): ResourceCollection
    {
        $user = $this->loggedinUser();
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 20);
        $sort = (string) $request->query('sort', 'updated_at:desc');

        $lists = $this->service->getListsForUser($user, $page, $perPage, $sort);

        return MyListShowResource::collection($lists);
    }

    /**
     * マイリストを作成
     */
    public function store(StoreMyListRequest $request): JsonResponse
    {
        $user = $this->loggedinUser();
        /** @var string $title */
        $title = $request->validated('title');
        /** @var string|null $note */
        $note = $request->validated('note');
        $isPublic = (bool) $request->validated('is_public', false);

        $list = $this->service->createList($user, $title, $note, $isPublic);

        return (new MyListShowResource($list))->response()->setStatusCode(201);
    }

    /**
     * マイリストを更新
     */
    public function update(UpdateMyListRequest $request, MyList $mylist): MyListShowResource
    {
        $this->authorize('update', $mylist);

        /** @var string $title */
        $title = $request->validated('title');
        /** @var string|null $note */
        $note = $request->validated('note');
        $isPublic = (bool) $request->validated('is_public', false);

        $updatedList = $this->service->updateList($mylist, $title, $note, $isPublic);

        return new MyListShowResource($updatedList);
    }

    /**
     * マイリストを削除
     */
    public function destroy(MyList $mylist): JsonResponse
    {
        $this->authorize('delete', $mylist);

        $this->service->deleteList($mylist);

        return response()->json(status: 200);
    }

    /**
     * リスト内のアイテム一覧取得
     */
    public function getItems(Request $request, MyList $mylist): ResourceCollection
    {
        $this->authorize('view', $mylist);

        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 20);
        $sort = (string) $request->query('sort', 'position');

        $items = $this->service->getItemsForList($mylist, $page, $perPage, $sort);

        return MyListItemResource::collection($items);
    }

    /**
     * リストにアイテムを追加
     */
    public function storeItem(StoreMyListItemRequest $request, MyList $mylist): JsonResponse
    {
        $this->authorize('view', $mylist);

        /** @var int $articleId */
        $articleId = $request->validated('article_id');
        /** @var string|null $note */
        $note = $request->validated('note');
        $article = Article::findOrFail($articleId);

        try {
            $item = $this->service->addItemToList($mylist, $article, $note);

            return (new MyListItemResource($item))->response()->setStatusCode(201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'article_id' => [$e->getMessage()],
                ],
            ], 422);
        }
    }

    /**
     * アイテムをアップデート
     */
    public function updateItem(UpdateMyListItemRequest $request, MyList $mylist, MyListItem $item): MyListItemResource
    {
        $this->authorize('update', $mylist);
        abort_if($item->list_id !== $mylist->id, 404);

        /** @var string|null $note */
        $note = $request->validated('note');
        /** @var int|null $position */
        $position = $request->validated('position');

        $updatedItem = $this->service->updateItem($item, $note, $position);

        return new MyListItemResource($updatedItem);
    }

    /**
     * アイテムを削除
     */
    public function destroyItem(MyList $mylist, MyListItem $item): JsonResponse
    {
        $this->authorize('delete', $mylist);
        abort_if($item->list_id !== $mylist->id, 404);

        $this->service->removeItem($item);

        return response()->json(status: 200);
    }

    /**
     * アイテムをまとめて並び替え
     */
    public function reorderItems(ReorderMyListItemsRequest $request, MyList $mylist): JsonResponse
    {
        $this->authorize('update', $mylist);

        /** @var array<int, array<string, int|string>> $items */
        $items = $request->validated('items');
        $this->service->reorderItems($mylist, $items);

        return response()->json(status: 200);
    }

    /**
     * 公開リストを表示（認証不要）
     */
    public function showPublic(string $slug): ResourceCollection
    {
        $list = $this->service->getPublicListBySlug($slug);

        $page = (int) request()->query('page', 1);
        $perPage = (int) request()->query('per_page', 20);
        $sort = (string) request()->query('sort', 'position');

        $items = $this->service->getPublicItemsForList($list, $page, $perPage, $sort);

        return MyListItemResource::collection($items)
            ->additional([
                'list' => new MyListShowResource($list),
            ]);
    }
}
