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
        $mylist = $this->service->getPublicListBySlug($slug);

        return view('pages.mylist.show', [
            'mylist' => $mylist,
        ]);
    }
}
