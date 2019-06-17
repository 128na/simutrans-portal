<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * ヘッダーデータを追加してビューを返す
     * @param string $view_path
     * @param array|void $data
     * @return Illuminate\View\View
     */
    protected static function viewWithHeader($view_path, $data = [])
    {
        $categories = Category::orderBy('order')->withCount('articles')->get();
        $data['categories'] = $categories->separateByType();
        return view($view_path, $data);
    }
}
