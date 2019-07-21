<?php

namespace App\Http\Controllers\Front;

use App\Events\ArticleConversion;
use App\Events\ArticleShown;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Breadcrumb;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * アドオン記事一覧
     */
    public function addons()
    {
        $articles = Article::addon()->active()->withForList()->paginate(24);

        $title = __('Articles');
        $breadcrumb = Breadcrumb::forList('Articles');
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
    }

    /**
     * アドオンランキング一覧
     */
    public function ranking()
    {
        $articles = Article::addon()->ranking()->active()->withForList()->paginate(24);

        $title = __('Ranking');
        $breadcrumb = Breadcrumb::forList('Ranking');
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
    }

    /**
     * 一般記事一覧
     */
    public function pages()
    {
        $articles = Article::withoutAnnounce()->active()->withForList()->paginate(24);

        $title = __('Pages');
        $breadcrumb = Breadcrumb::forList('Pages');
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
    }

    /**
     * 一般記事一覧
     */
    public function announces()
    {
        $articles = Article::announce()->active()->withForList()->paginate(24);

        $title = __('Announces');
        $breadcrumb = Breadcrumb::forList('Announces');
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
        $canonical_url = route('articles.show', $article->slug);
        return static::viewWithHeader('front.articles.show', compact('article', 'breadcrumb', 'canonical_url'));
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
        $method = Str::camel($type);

        try {
            $category = Category::$method()->slug($slug)->firstOrFail();
        } catch (\BadMethodCallException $e) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException($e);
        }
        $articles = $category->articles()
            ->active()->withForList()->paginate(24);

        $title = __('Category :name', ['name' => __("category.{$type}.{$category->slug}")]);
        $breadcrumb = Breadcrumb::forCategory($category);
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
    }

    /**
     * カテゴリ(pak/addon)の投稿一覧画面
     */
    public function categoryPakAddon($pak_slug, $addon_slug)
    {
        $pak   = Category::pak()->slug($pak_slug)->firstOrFail();
        $addon = Category::addon()->slug($addon_slug)->firstOrFail();

        $articles = Article::active()
            ->whereHas('categories', function($query) use ($pak) {
                $query->pak()->slug($pak->slug);
            })
            ->whereHas('categories', function($query) use ($addon) {
                $query->addon()->slug($addon->slug);
            })
            ->withForList()->paginate(24);
        $title = __(':pak, :addon', ['pak' => __('category.pak.'.$pak->slug), 'addon' => __('category.addon.'.$addon->slug)]);
        $breadcrumb = Breadcrumb::forPakAddon($pak->slug, $addon->slug);
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
    }

    /**
     * タグの投稿一覧画面
     */
    public function tag(Tag $tag)
    {
        $articles = $tag->articles()
            ->active()->withForList()->paginate(24);

        $title = __('Tag :name', ['name' => $tag->name]);
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
            ->active()->withForList()->paginate(24);

        $title = __('User :name', ['name' => $user->name]);
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
        $articles = Article::active()->search($word)->withForList()->paginate(24);

        $title = __('Search results by :word', ['word' => $word]);
        $breadcrumb = Breadcrumb::forSearch($word);
        return static::viewWithHeader('front.articles.index', compact('title', 'articles', 'breadcrumb'));
    }
}
