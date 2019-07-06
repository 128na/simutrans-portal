<?php

namespace App\Http\Controllers;

use App\Models\Article;

/**
 * トップページ
 */
class FrontController extends Controller
{
    public function index()
    {
        $data = [
            'articles' => [
                'announces' => Article::announce()->active()->withForList()->limit(3)->get(),
                'pages'     => Article::withoutAnnounce()->active()->withForList()->limit(3)->get(),
                'latest'    => Article::addon()->active()->withForList()->limit(3)->get(),
                'ranking'   => Article::addon()->ranking()->active()->withForList()->limit(3)->get(),
            ]
        ];

        return static::viewWithHeader('front.index', $data);
    }

}
