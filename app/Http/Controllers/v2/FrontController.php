<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Enums\CategoryType;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class FrontController extends Controller
{
    public function top()
    {
        return view('v2.top.index', [
            'announces' => $this->getTopAnnounces(),
        ]);
    }
    public function pak128jp()
    {
        return view('v2.pak.index', [
            'pak' => '128-japan',
            'articles' => $this->getLatest('128-japan'),
        ]);
    }
    public function pak128()
    {
        return view('v2.pak.index', [
            'pak' => '128',
            'articles' => $this->getLatest('128'),
        ]);
    }
    public function pak64()
    {
        return view('v2.pak.index', [
            'pak' => '64',
            'articles' => $this->getLatest('64'),
        ]);
    }
    public function pakOthers()
    {
        return view('v2.pak.index', [
            'pak' => 'other-pak',
            'articles' => $this->getLatestOther(),
        ]);
    }
    public function announces()
    {
        return view('v2.announce.index', [
            'articles' => $this->getAnnounces(),
        ]);
    }

    public function show(string $userIdOrNickname, string $slug)
    {

        return view('v2.show.index', [
            'article' => $this->get($userIdOrNickname, $slug),
        ]);
    }

    public function fallback(Request $request)
    {
        return view('v2.top');
    }

    private function get(string $userIdOrNickname, string $slug): Article
    {
        $query = \App\Models\Article::query()
            ->select(['articles.*'])
            ->withoutGlobalScopes()
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->where('articles.status', ArticleStatus::Publish)
            ->where('articles.slug', urlencode($slug))
            ->whereNull('articles.deleted_at')
            ->whereNull('users.deleted_at')
            ->orderBy('articles.published_at', 'desc')
            ->with('categories', 'tags', 'attachments.fileInfo', 'user.profile.attachments', 'articles.user', 'relatedArticles.user');

        if (is_numeric($userIdOrNickname)) {
            $query->where('articles.user_id', $userIdOrNickname);
        } else {
            $query->where('users.nickname', $userIdOrNickname);
        }

        return $query->firstOrFail();
    }

    private function getLatest(string $pak): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return \App\Models\Article::query()
            ->select(['articles.*'])
            ->withoutGlobalScopes()
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->join('article_category as ac', 'articles.id', '=', 'ac.article_id')
            ->join('categories as c', 'ac.category_id', '=', 'c.id')
            ->where('c.type', CategoryType::Pak)
            ->where('c.slug', $pak)
            ->where('articles.status', ArticleStatus::Publish)
            ->whereIn('articles.post_type', [ArticlePostType::AddonIntroduction, ArticlePostType::AddonPost])
            ->whereNull('articles.deleted_at')
            ->whereNull('users.deleted_at')
            ->orderBy('articles.published_at', 'desc')
            ->with('categories', 'tags', 'attachments', 'user.profile.attachments')
            ->paginate(30);
    }

    private function getLatestOther(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $excludeSlugs = ['64', '128', '128-japan'];

        return \App\Models\Article::query()
            ->select('articles.*')
            ->withoutGlobalScopes()
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->where('articles.status', ArticleStatus::Publish)
            ->whereNull('articles.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereNotExists(function ($q) use ($excludeSlugs) {
                $q->selectRaw(1)
                    ->from('article_category as ac')
                    ->join('categories as c', 'ac.category_id', '=', 'c.id')
                    ->whereColumn('ac.article_id', 'articles.id')
                    ->where('c.type', CategoryType::Pak)
                    ->whereIn('c.slug', $excludeSlugs);
            })
            ->orderByDesc('articles.published_at')
            ->with(['categories', 'tags', 'attachments', 'user.profile.attachments'])
            ->paginate(30);
    }

    private function getAnnounces(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return \App\Models\Article::query()
            ->select('articles.*', 'users.nickname as user_nickname')
            ->withoutGlobalScopes()
            ->join('article_category as ac', 'articles.id', '=', 'ac.article_id')
            ->join('categories as c', 'ac.category_id', '=', 'c.id')
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->where('articles.status', ArticleStatus::Publish)
            ->whereIn('articles.post_type', [ArticlePostType::Page, ArticlePostType::Markdown])
            ->whereNull('articles.deleted_at')
            ->where('c.type', CategoryType::Page)
            ->where('c.slug', 'announce')
            ->whereNull('users.deleted_at')
            ->orderBy('articles.published_at', 'desc')
            ->with(['categories', 'tags', 'attachments', 'user.profile.attachments'])
            ->paginate(30);
    }

    private function getTopAnnounces(): \Illuminate\Database\Eloquent\Collection
    {
        return \App\Models\Article::query()
            ->select('articles.*', 'users.nickname as user_nickname')
            ->withoutGlobalScopes()
            ->join('article_category as ac', 'articles.id', '=', 'ac.article_id')
            ->join('categories as c', 'ac.category_id', '=', 'c.id')
            ->join('users', 'articles.user_id', '=', 'users.id')
            ->where('articles.status', ArticleStatus::Publish)
            ->whereIn('articles.post_type', [ArticlePostType::Page, ArticlePostType::Markdown])
            ->whereNull('articles.deleted_at')
            ->where('c.type', CategoryType::Page)
            ->where('c.slug', 'announce')
            ->whereNull('users.deleted_at')
            ->orderBy('articles.published_at', 'desc')
            ->limit(3)
            ->get();
    }
}
