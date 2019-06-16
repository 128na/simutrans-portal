<?php

namespace App\Http\Controllers\Mypage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;

class ArticleController extends Controller
{
    //

    public function create()
    {
        return view('mypage.articles.create');
    }
    public function store(Request $request)
    {

    }
    public function edit(Article $article)
    {
        return view('mypage.articles.edit', compact('article'));
    }
    public function update(Request $request, Article $article)
    {

    }
}
