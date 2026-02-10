<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Resources\Mypage\MyListItem as MyListItemResource;
use App\Http\Resources\Mypage\MyListShow as MyListShowResource;
use App\Services\MyListService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;

class PublicMyListController extends Controller
{
    public function __construct(private readonly MyListService $service) {}

    public function show(string $slug): View
    {
        $mylist = $this->service->getPublicListBySlug($slug);

        return view('pages.mylist.show', [
            'mylist' => $mylist,
        ]);
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
