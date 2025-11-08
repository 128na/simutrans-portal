<?php

declare(strict_types=1);

namespace App\Http\Controllers\v2\Mypage;

use App\Http\Resources\v2\ArticleEdit;
use App\Http\Resources\v2\Attachment;
use App\Models\Article;
use App\Repositories\v2\ArticleRepository;
use App\Repositories\v2\CategoryRepository;
use App\Repositories\v2\TagRepository;
use App\Services\Front\MetaOgpService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

final class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly TagRepository $tagRepository,
        private readonly MetaOgpService $metaOgpService,
    ) {}

    public function index(): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        return view('v2.mypage.articles', [
            'user' => $user->only(['id', 'name', 'nickname']),
            'articles' => $user
                ->articles()
                ->select('id', 'title', 'slug', 'status', 'post_type', 'published_at', 'modified_at')
                ->with('attachments', 'totalConversionCount', 'totalViewCount')
                ->get(),
            'meta' => $this->metaOgpService->articleIndex(),
        ]);
    }

    public function create(): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        if ($user->cannot('store', Article::class)) {
            return abort(403);
        }

        return view('v2.mypage.article-create', [
            'user' => $user->only(['id', 'name', 'nickname', 'role']),
            'attachments' => Attachment::collection($user->myAttachments()->with('fileInfo')->get()),
            'categories' => $this->categoryRepository->getForSearch()->groupBy('type'),
            'tags' => $this->tagRepository->getForEdit(),
            'relationalArticles' => $this->articleRepository->getForEdit(),
            'meta' => $this->metaOgpService->articleCreate(),
        ]);
    }

    public function store(Request $request): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        if ($user->cannot('store', Article::class)) {
            return abort(403);
        }

        // TODO: store logic
        return view('v2.mypage.index', [
            'user' => $user,
            'meta' => $this->metaOgpService->articleCreate(),
        ]);
    }

    public function edit(Article $article): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        if ($user->cannot('update', $article)) {
            return abort(403);
        }

        return view('v2.mypage.article-edit', [
            'user' => $user->only(['id', 'name', 'nickname', 'role']),
            'article' => new ArticleEdit($article->load('categories', 'tags', 'articles', 'attachments')),
            'attachments' => Attachment::collection($user->myAttachments()->with('fileInfo')->get()),
            'categories' => $this->categoryRepository->getForSearch()->groupBy('type'),
            'tags' => $this->tagRepository->getForEdit(),
            'relationalArticles' => $this->articleRepository->getForEdit($article),
            'meta' => $this->metaOgpService->articleEdit(),
        ]);
    }

    public function update(Request $request, Article $article): \Illuminate\Contracts\View\View
    {
        $user = Auth::user();
        if ($user->cannot('update', $article)) {
            return abort(403);
        }

        // TODO: update logic
        return view('v2.mypage.index', [
            'user' => $user,
            'meta' => $this->metaOgpService->articleEdit(),
        ]);
    }
}
