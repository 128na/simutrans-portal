<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Events\ArticleShown;
use App\Events\ArticleConversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if(Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new ArticleShown($article));
        }

        $article->load('user', 'attachments', 'categories');
        return static::viewWithHeader('front.articles.show', compact('article'));
    }
    public function download(Article $article)
    {
        abort_unless($article->is_publish, 404);

        if(Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new ArticleConversion($article));
        }

        $article->load('attachments');
        abort_unless($article->has_file, 404);

        return response()
            ->download(public_path('storage/'.$article->file->path), $article->file->original_name);
    }
}
