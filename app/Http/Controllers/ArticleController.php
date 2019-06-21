<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Events\ArticleShown;
use App\Events\ArticleConversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * 記事一覧
     */
    public function index()
    {
        $articles = Article::active()->withForList()->latest()->paginate(20);

        $title = 'Top';
        return static::viewWithHeader('front.articles.index', compact('title', 'articles'));
    }

    /**
     * 記事詳細
     */
    public function show(Article $article)
    {
        abort_unless($article->is_publish, 404);

        if(Auth::check() === false || Auth::id() !== $article->user_id) {
            event(new ArticleShown($article));
        }

        $article->load('user', 'attachments', 'categories', 'tags');
        return static::viewWithHeader('front.articles.show', compact('article'));
    }

    /**
     * アドオンダウンロード
     */
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

    /**
     * カテゴリ(slug)の投稿一覧画面
     */
    public function category($type, $slug)
    {
        $category = Category::$type()->where('slug', $slug)->firstOrFail();
        $articles = $category->articles()
            ->active()->withForList()->latest()->paginate(20);

        $title = 'Category '.$category->name;
        return static::viewWithHeader('front.articles.index', compact('title', 'articles'));
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧画面
     */
    public function categoryPakAddon($pak, $addon)
    {
        $articles = Article::active()
            ->whereHas('categories', function($query) use ($pak) {
                $query->where('type', 'pak')->where('slug', $pak);
            })
            ->whereHas('categories', function($query) use ($addon) {
                $query->where('type', 'addon')->where('slug', $addon);
            })
            ->withForList()->latest()->paginate(20);
        $title = 'Pak '.$pak.', '.$addon;
        return static::viewWithHeader('front.articles.index', compact('title', 'articles'));
    }

    /**
     * タグの投稿一覧画面
     */
    public function tag(Tag $tag)
    {
        $articles = $tag->articles()
            ->active()->withForList()->latest()->paginate(20);

        $title = 'Tag '.$tag->name;
        return static::viewWithHeader('front.articles.index', compact('title', 'articles'));
    }

    /**
     * ユーザーの投稿一覧画面
     */
    public function user(User $user)
    {
        abort_if($user->isAdmin(), 404);
        $articles = $user->articles()
            ->active()->withForList()->latest()->paginate(20);

        $title = 'User '.$user->name;
        return static::viewWithHeader('front.articles.index', compact('title', 'articles'));
    }


}
