<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Models\MyList;
use Illuminate\Contracts\View\View;

class MyListPageController extends Controller
{
    public function index(): View
    {
        return view('mypage.mylists');
    }

    public function show(MyList $mylist): View
    {
        $this->authorize('view', $mylist);

        return view('mypage.mylist-detail', [
            'mylist' => [
                'id' => $mylist->id,
                'title' => $mylist->title,
                'note' => $mylist->note,
                'is_public' => $mylist->is_public,
                'slug' => $mylist->slug,
                'created_at' => $mylist->created_at,
                'updated_at' => $mylist->updated_at,
            ],
        ]);
    }
}
