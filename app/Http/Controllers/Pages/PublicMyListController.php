<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Services\MyListService;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

class PublicMyListController extends Controller
{
    public function __construct(private readonly MyListService $service) {}

    public function show(string $slug): View
    {
        $list = $this->service->getPublicListBySlug($slug);
        abort_if(! $list, 404);

        return view('pages.mylist.show', [
            'mylist' => [
                'id' => $list->id,
                'title' => $list->title,
                'note' => $list->note,
                'is_public' => $list->is_public,
                'slug' => $list->slug,
                'created_at' => $list->created_at,
                'updated_at' => $list->updated_at,
            ],
        ]);
    }
}
