<?php

namespace App\Http\Controllers\Mypage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;

class ArticleController extends Controller
{
    //

    public function create()
    {
        $categories = Category::all()->separateByType();
        $post_categories = $categories->get('post');
        return view('mypage.articles.create', compact('post_categories', 'categories'));
    }
    public function store(Request $request)
    {

    }
    public function edit(Article $article)
    {
        $article->load('categories', 'attachments');
        $categories = Category::all()->separateByType();
        return view('mypage.articles.edit', compact('article', 'categories'));
    }
    public function update(Request $request, Article $article)
    {

    }
}
