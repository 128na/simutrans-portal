<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\ArticleService;

/**
 * トップページ.
 */
class IndexController extends Controller
{
    private ArticleService $article_service;

    public function __construct(
        ArticleService $article_service
    ) {
        $this->article_service = $article_service;
    }

    public function index()
    {
        $contents = $this->article_service->getTopContents();

        return view('front.index', $contents);
    }

    public function language($name)
    {
        if (array_key_exists($name, config('languages'))) {
            \App::setLocale($name);

            return redirect()->back(307, ['Cache-Control' => 'no-store'])->withCookie('lang', $name);
        }

        return redirect()->back();
    }
}
