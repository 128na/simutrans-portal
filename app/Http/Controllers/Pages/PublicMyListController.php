<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Actions\FrontMyList\PublicListAction;
use App\Actions\FrontMyList\PublicShowAction;
use App\Http\Resources\Mypage\MyListItem as MyListItemResource;
use App\Http\Resources\Mypage\MyListShow as MyListShowResource;
use App\Services\MyListService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class PublicMyListController extends Controller
{
    public function __construct(
        private readonly MyListService $service,
        private readonly PublicListAction $publicListAction,
        private readonly PublicShowAction $publicShowAction,
    ) {}

    public function index(): View
    {
        return view('pages.mylist.index');
    }

    public function show(string $slug): View
    {
        $mylist = $this->service->getPublicListBySlug($slug);

        return view('pages.mylist.show', [
            'mylist' => $mylist,
        ]);
    }

    /**
     * 公開リスト一覧を取得（認証不要）
     */
    public function listPublic(): ResourceCollection
    {
        $page = (int) request()->query('page', 1);
        $perPage = (int) request()->query('per_page', 20);
        $sort = (string) request()->query('sort', 'updated_at:desc');

        $paginator = ($this->publicListAction)($page, $perPage, $sort);

        return MyListShowResource::collection($paginator);
    }

    /**
     * 公開リストを表示（認証不要）
     */
    public function showPublic(string $slug): ResourceCollection
    {
        $page = (int) request()->query('page', 1);
        $perPage = (int) request()->query('per_page', 20);
        $sort = (string) request()->query('sort', 'position');

        $result = ($this->publicShowAction)($slug, $page, $perPage, $sort);
        $list = $result['list'];
        $items = $result['items'];

        return MyListItemResource::collection($items)
            ->additional([
                'list' => new MyListShowResource($list),
            ]);
    }
}
