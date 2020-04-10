<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ArticleService;

/**
 * トップページ
 */
class IndexController extends Controller
{
    /**
     * @var ArticleService
     */
    private $article_service;

    public function __construct(ArticleService $article_service)
    {
        $this->article_service = $article_service;
    }

    public function index()
    {
        $contents = $this->article_service->getTopContents();
        $contents = array_merge($contents, $this->article_service->getHeaderContents());

        return view('front.index', $contents);
    }

    public function language($name)
    {
        if (array_key_exists($name, config('languages'))) {
            \App::setLocale($name);
            session()->flash('success', __('Set language.'));
            return redirect()->back()->withCookie('lang', $name);
        } else {
            session()->flash('error', __('This language is not supported.'));
        }
        return redirect()->back();
    }
}
