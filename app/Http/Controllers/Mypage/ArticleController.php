<?php

namespace App\Http\Controllers\Mypage;

use App\Http\Controllers\Controller;
use App\Http\Resources\Article as ArticleResouce;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

/**
 * 記事CRUD共通コントローラー
 */
class ArticleController extends Controller
{
    /**
     * 登録画面
     */
    public function create()
    {
        return view('mypage.articles.create');
    }

    /**
     * 更新画面
     */
    public function edit(Article $article)
    {
        $article->load('categories', 'attachments', 'tags');

        $article = new ArticleResouce($article);
        return view('mypage.articles.edit', compact('article'));
    }

    public function analytics()
    {
        $articles = Auth::user()->articles()
            ->with('viewCounts', 'conversionCounts')->get();
        $articles = self::toSlim($articles);

        return view('mypage.analytics', compact('articles'));
    }

    /**
     * チャート用に加工
     */
    private static function toSlim($articles)
    {
        return $articles->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'url' => route('articles.show', $article->slug),
                'updated_at' => $article->updated_at->format('Ymd'),
                'created_at' => $article->created_at->format('Ymd'),
                'checked' => false,
                'conversion_counts' => $article->conversionCounts->reduce(function ($acc, $c) {
                    $acc[$c->period] = $c->count;
                    return $acc;
                }, []),
                'view_counts' => $article->viewCounts->reduce(function ($acc, $c) {
                    $acc[$c->period] = $c->count;
                    return $acc;
                }, []),
            ];
        });
    }
}
