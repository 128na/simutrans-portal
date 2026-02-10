<?php

declare(strict_types=1);

namespace App\Actions\FrontMyList;

use App\Services\MyListService;
use Illuminate\Contracts\Pagination\Paginator;

class PublicListAction
{
    public function __construct(private MyListService $myListService) {}

    /**
     * @return Paginator<int, \App\Models\MyList>
     */
    public function __invoke(int $page = 1, int $perPage = 20, string $sort = 'updated_at:desc'): Paginator
    {
        return $this->myListService->getPublicLists($page, $perPage, $sort);
    }
}
