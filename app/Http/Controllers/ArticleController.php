<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Events\ArticleShown;
use App\Events\ArticleConversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::active()->withForList()->latest()->paginate(20);

        $title = 'Top';
        return static::viewWithHeader('front.articles.index', compact('title', 'articles'));
    }

    public function show(Article $article)
    {
        abort_unless($article->is_publish, 404);

        if(Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new ArticleShown($article));
        }

        $article->load('user', 'attachments', 'categories', 'tags');
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

    public function category($type, $slug)
    {
        $category = Category::$type()->where('slug', $slug)->firstOrFail();
        $articles = $category->articles()
            ->active()->withForList()->latest()->paginate(20);

        $title = 'Category '.$category->name;
        return static::viewWithHeader('front.articles.index', compact('title', 'articles'));
    }

    public function tag(Tag $tag)
    {
        $articles = $tag->articles()
            ->active()->withForList()->latest()->paginate(20);

        $title = 'Tag '.$tag->name;
        return static::viewWithHeader('front.articles.index', compact('title', 'articles'));
    }

}
