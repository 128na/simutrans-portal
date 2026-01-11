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

        return view('mypage.mylist-show', [
            'mylist' => $mylist,
        ]);
    }
}
