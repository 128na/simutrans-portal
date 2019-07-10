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
        $announces = Article::announce()->active()->withForList()->limit(3)->get();
        $pages     = Article::withoutAnnounce()->active()->withForList()->limit(3)->get();
        $latest    = Article::addon()->active()->withForList()->limit(3)->get();
        $ranking   = Article::addon()->ranking()->active()->whereNotIn('articles.id', $latest->pluck('id'))->withForList()->limit(3)->get();


        $data = [
            'articles' => [
                'announces' => $announces,
                'pages'     => $pages,
                'latest'    => $latest,
                'ranking'   => $ranking,
            ]
        ];

        return static::viewWithHeader('front.index', $data);
    }

}
