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
        $latest = [
            '64' => Article::pak('64')->addon()->active()->withForList()->limit(6)->get(),
            '128' => Article::pak('128')->addon()->active()->withForList()->limit(6)->get(),
            '128-japan' => Article::pak('128-japan')->addon()->active()->withForList()->limit(6)->get(),
        ];
        $excludes = collect($latest)->flatten()->pluck('id')->unique()->toArray();
        $ranking = Article::addon()->ranking()->active()->whereNotIn('articles.id', $excludes)->withForList()->limit(6)->get();

        return static::viewWithHeader('front.index', compact('announces', 'pages', 'latest', 'ranking'));
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
