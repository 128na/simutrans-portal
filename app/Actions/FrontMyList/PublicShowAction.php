<?php

declare(strict_types=1);

namespace App\Actions\FrontMyList;

use App\Models\MyList;
use App\Services\MyListService;
use Illuminate\Contracts\Pagination\Paginator;

class PublicShowAction
{
    public function __construct(private MyListService $myListService) {}

    /**
     * @return array{list: MyList, items: Paginator<int, \App\Models\MyListItem>}
     */
    public function __invoke(string $slug, int $page = 1, int $perPage = 20, string $sort = 'position'): array
    {
        $list = $this->myListService->getPublicListBySlug($slug);
        $items = $this->myListService->getPublicItemsForList($list, $page, $perPage, $sort);

        return [
            'list' => $list,
            'items' => $items,
        ];
    }
}
