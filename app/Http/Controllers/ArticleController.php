<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Breadcrumb;
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
     * アドオン記事一覧
     */
    public function addons()
    {
        $articles = Article::addon()->active()->withForList()->paginate(20);

        $title = __('message.articles');
        $breadcrumb = Breadcrumb::forList('articles');
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
    }
    /**
     * 一般記事一覧
     */
    public function pages()
    {
        $articles = Article::withoutAnnounce()->active()->withForList()->paginate(20);

        $title = __('message.pages');
        $breadcrumb = Breadcrumb::forList('pages');
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
    }
    /**
     * 一般記事一覧
     */
    public function announces()
    {
        $articles = Article::announce()->active()->withForList()->paginate(20);

        $title = __('message.announces');
        $breadcrumb = Breadcrumb::forList('announces');
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
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
        $breadcrumb = Breadcrumb::forShow($article);
        return static::viewWithHeader('front.articles.show', compact('article', 'breadcrumb'));
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
            ->active()->withForList()->paginate(20);

        $title = __('message.of-category', ['name' => __("category.{$type}.{$category->slug}")]);
        $breadcrumb = Breadcrumb::forCategory($category);
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
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
            ->withForList()->paginate(20);
        $title = __('message.of-pak-addon', ['pak' => __('category.pak.'.$pak), 'addon' => __('category.addon.'.$addon)]);
        $breadcrumb = Breadcrumb::forPakAddon($pak, $addon);
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
    }

    /**
     * タグの投稿一覧画面
     */
    public function tag(Tag $tag)
    {
        $articles = $tag->articles()
            ->active()->withForList()->paginate(20);

        $title = __('message.of-tag', ['name' => $tag->name]);
        $breadcrumb = Breadcrumb::forTag($tag->name);
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
    }

    /**
     * ユーザーの投稿一覧画面
     */
    public function user(User $user)
    {
        $user->load('profile','profile.attachments');
        $articles = $user->articles()
            ->active()->withForList()->paginate(20);

        $title = __('message.by-user', ['name' => $user->name]);
        $breadcrumb = Breadcrumb::forUser($user);
        return static::viewWithHeader('front.articles.index', compact('title', 'user', 'articles', 'breadcrumb'));
    }

    /**
     * 検索結果一覧
     */
    public function search(Request $request)
    {
        $word = $request->input('s');
        if(is_null($word)) {
            return redirect()->route('addons.index');
        }
        $articles = Article::active()->search($word)->withForList()->paginate(20);

        $title = __('message.result-of', ['word' => $word]);
        $breadcrumb = Breadcrumb::forSearch($word);
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
    }
}
