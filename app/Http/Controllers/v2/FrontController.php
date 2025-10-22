<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2;

use App\Enums\ArticlePostType;
use App\Enums\ArticleStatus;
use App\Events\ArticleConversion;
use App\Events\ArticleShown;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\v2\ArticleRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class FrontController extends Controller
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) {}
    public function top()
    {
        return view('v2.top.index', [
            'announces' => $this->articleRepository->getTopAnnounces(),
        ]);
    }
    public function pak128jp()
    {
        return view('v2.pak.index', [
            'pak' => '128-japan',
            'articles' => $this->articleRepository->getLatest('128-japan'),
        ]);
    }
    public function pak128()
    {
        return view('v2.pak.index', [
            'pak' => '128',
            'articles' => $this->articleRepository->getLatest('128'),
        ]);
    }
    public function pak64()
    {
        return view('v2.pak.index', [
            'pak' => '64',
            'articles' => $this->articleRepository->getLatest('64'),
        ]);
    }
    public function pakOthers()
    {
        return view('v2.pak.index', [
            'pak' => 'other-pak',
            'articles' => $this->articleRepository->getLatestOther(),
        ]);
    }

    public function announces()
    {
        return view('v2.announce.index', [
            'articles' => $this->articleRepository->getAnnounces(),
        ]);
    }

    public function show(string $userIdOrNickname, string $slug)
    {
        $article = $this->articleRepository->findOrFail($userIdOrNickname, $slug);
        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            ArticleShown::dispatch($article);
        }

        return view('v2.show.index', [
            'article' => $article,
        ]);
    }
    public function search(Request $request)
    {
        $condition = $request->all();

        return view('v2.search.index', [
            'condition' => $condition,
            'options' => $this->getSearchOptions(),
            'articles' => $this->articleRepository->search($condition),
        ]);
    }

    public function fallbackShow(string $slugOrId)
    {
        $article = is_numeric($slugOrId)
            ? Article::findOrFail($slugOrId)
            : Article::slug($slugOrId)->orderBy('id', 'asc')->firstOrFail();

        return redirect(route('articles.show', ['userIdOrNickname' => $article->user?->nickname ?? $article->user_id, 'articleSlug' => $article->slug]), 302);
    }

    public function download(Article $article)
    {
        abort_unless($article->is_publish, 404);
        abort_unless($article->is_addon_post, 404);
        abort_unless($article->has_file && $article->file, 404);

        if (Auth::check() === false || Auth::id() !== $article->user_id) {
            ArticleConversion::dispatch($article);
        }

        return $this->getPublicDisk()->download(
            $article->file->path,
            $article->file->original_name
        );
    }


    private function getSearchOptions(): array
    {
        return [
            'categories' => Category::query()
                ->select(['categories.id', 'categories.type', 'categories.slug'])
                ->orderBy('order', 'asc')
                ->get(),
            'tags' => Tag::query()
                ->select(['tags.id', 'tags.name'])
                ->orderBy('name', 'asc')
                ->get(),
            'users' => User::query()
                ->select(['users.id', 'users.nickname', 'users.name'])
                ->whereExists(
                    fn($q) => $q->selectRaw(1)
                        ->from('articles as a')
                        ->whereColumn('a.user_id', 'users.id')
                        ->where('a.status', ArticleStatus::Publish)
                )
                ->orderBy('name', 'asc')
                ->get(),
            'postTypes' => ArticlePostType::cases(),
        ];
    }
}
