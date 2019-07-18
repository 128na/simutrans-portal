<?php

namespace App\Http\Controllers\Front;

use App\Models\Article;
use App\Http\Controllers\Controller;

/**
 * トップページ
 */
class IndexController extends Controller
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

    public function language($name) {
        if(array_key_exists($name, config('languages'))) {
            \App::setLocale($name);
            session()->flash('success', __('Set language.'));
            return redirect()->back()->withCookie('lang', $name);
        } else {
            session()->flash('error', __('This language is not supported.'));
        }
        return redirect()->back();
    }
}
