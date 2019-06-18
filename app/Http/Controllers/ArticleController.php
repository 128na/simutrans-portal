<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::active()->with('user', 'attachments', 'categories')->latest()->paginate(20);
        return static::viewWithHeader('front.articles.index', compact('articles'));
    }

    public function show(Article $article)
    {
        abort_unless($article->is_publish, 404);

        $article->load('user', 'attachments', 'categories');
        return static::viewWithHeader('front.articles.show', compact('article'));
    }
    public function download(Article $article)
    {
        abort_unless($article->is_publish, 404);

        $article->load('attachments');
        abort_unless($article->has_file, 404);

        return response()
            ->download(public_path('storage/'.$article->file->path), $article->file->original_name);
    }
}
