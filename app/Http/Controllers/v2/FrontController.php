<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

final class FrontController extends Controller
{
    public function top()
    {
        return view('v2.top', [
            'latest' => $this->getLatest(),
            'announces' => $this->getAnnounces(),
        ]);
    }

    public function fallback(Request $request)
    {
        return view('v2.top');
    }

    private function getLatest(): \Illuminate\Database\Eloquent\Collection
    {
        $rows = DB::table('articles')
            ->where('articles.status', ArticleStatus::Publish)
            ->whereIn('articles.post_type', [ArticlePostType::AddonIntroduction, ArticlePostType::AddonPost])
            ->whereNull('articles.deleted_at')
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->whereNull('users.deleted_at')
            ->orderBy('articles.published_at', 'desc')
            ->limit(5)
            ->get(['articles.*', 'users.nickname as user_nickname']);

        return \App\Models\Article::hydrate($rows->toArray())
            ->load('categories', 'tags');
    }

    private function getAnnounces(): \Illuminate\Database\Eloquent\Collection
    {
        $rows = DB::table('articles')
            ->where('articles.status', ArticleStatus::Publish)
            ->whereIn('articles.post_type', [ArticlePostType::Page, ArticlePostType::Markdown])
            ->whereNull('articles.deleted_at')
            ->join('article_category as ac', 'articles.id', '=', 'ac.article_id')
            ->join('categories as c', 'ac.category_id', '=', 'c.id')
            ->where('c.type', CategoryType::Page)
            ->where('c.slug', 'announce')
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->whereNull('users.deleted_at')
            ->orderBy('articles.published_at', 'desc')
            ->limit(3)
            ->get(['articles.*', 'users.nickname as user_nickname']);

        return \App\Models\Article::hydrate($rows->toArray())
            ->load('categories', 'tags');
    }
}
